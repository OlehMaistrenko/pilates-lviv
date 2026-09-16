/* ============================================================
   Карта локацій: Mapbox GL + маршрут до залу (partials/map.php; vendor
   підключає цей файл і mapbox-gl через $vendor_map лише коли є токен).
   Дані залів — data-* на <li> списку: той самий елемент і статичний
   фолбек, і джерело для JS, окремого JSON не треба.
   ============================================================ */
(() => {
  'use strict';

  const root = document.querySelector('[data-map]');
  if (!root || typeof window.mapboxgl === 'undefined' || !root.dataset.token) return;

  const section = root.closest('.map');
  const hint = section.querySelector('[data-map-hint]');
  const locateBtn = section.querySelector('[data-map-locate]');
  /* Тексти — з data-i18n (partials/map.php), щоб локалізувались разом
     зі сторінкою. Мова для чисел і Directions — з <html lang>. */
  const i18n = JSON.parse(root.dataset.i18n || '{}');
  const txt = (key, vars = {}) => (i18n[key] ?? '').replace(/\{(\w+)\}/g, (_, k) => vars[k] ?? '');
  const lang = document.documentElement.lang || 'uk';
  const halls = [...section.querySelectorAll('[data-hall]')].map((li) => ({
    li, slug: li.dataset.slug, lng: +li.dataset.lng, lat: +li.dataset.lat,
    name: li.querySelector('[data-map-pick]').textContent.trim(),
    /* адреса для тултипа — з того ж <li>, перший абзац після кнопки;
       окремого data-* не заводимо, елемент і так є джерелом даних */
    address: li.querySelector('p')?.textContent.trim() ?? '',
  }));
  /* reduced-motion: карта не летить, а стрибає — духу сторінки це не
     міняє, а людям із вестибулярними проблемами flyTo реально шкодить */
  const dur = matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 900;

  let map = null;
  let ready = false;      // 'load' карти минув — є джерело маршруту, можна fitBounds
  let pending = null;     // зал, обраний до 'load' — pick відкладається на onload
  let me = null;          // [lng, lat] відвідувача, після геолокації
  let meMarker = null;
  let tipPopup = null;    // один Popup на карту, переїжджає за активним залом
  /* Ширина тултипа в px. Одне число на два місця: maxWidth самого попапа
     і запас у fitPad() (щоб бульбашка не заходила під панель). Розходження
     між ними давало б саме той баг, тож тримаємо спільною константою. */
  const TIP_MAX_W = 240;
  let active = null;      // обраний зал
  let mode = 'walking';

  const say = (text) => { hint.textContent = text; hint.hidden = !text; };
  const label = (h, text) => {
    const out = h.li.querySelector('[data-map-route]');
    out.textContent = text ?? '';
    out.hidden = !text;
  };
  /* Маркер: Mapbox позиціює елемент інлайновим transform, тож масштаб
     активного маркера живе на вкладеному елементі, а не на тому, що
     віддаємо Marker-у */
  const markerEl = (tag, className, name) => {
    const wrap = document.createElement('div');
    const el = document.createElement(tag);
    el.className = className;
    if (tag === 'button') { el.type = 'button'; el.setAttribute('aria-label', name); }
    /* Маркер залу — крапля з левом (assets/icons/sprite.svg#icon-pin-lion).
       Через <use>, а не інлайновим path: той самий лев уже лежить у
       спрайті, і копія на кожен маркер була б трьома зайвими кілобайтами
       в DOM. Точка відвідувача (.map__me) лишається чистим CSS-кружком. */
    if (className === 'map__marker') {
      el.innerHTML = '<svg class="map__pin" viewBox="0 0 48 60" aria-hidden="true">'
        + '<use href="assets/icons/sprite.svg#icon-pin-lion"></use></svg>';
    }
    wrap.appendChild(el);
    return { wrap, el };
  };
  const fmtDist = (m) => m < 1000
    ? txt('m', { n: Math.round(m) })
    : txt('km', { n: (m / 1000).toLocaleString(lang, { minimumFractionDigits: 1, maximumFractionDigits: 1 }) });
  const fmtTime = (s) => {
    const h = Math.floor(s / 3600), m = Math.round((s % 3600) / 60);
    return h ? txt('hMin', { h, m }) : txt('min', { m });
  };
  /* Пряма відстань — щоб підписати ВСІ зали одразу після геолокації,
     не роблячи три запити в Directions заради «який ближче» */
  const haversine = (a, b) => {
    const R = 6371000, toRad = (d) => d * Math.PI / 180;
    const dLat = toRad(b[1] - a[1]), dLng = toRad(b[0] - a[0]);
    const x = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(a[1])) * Math.cos(toRad(b[1])) * Math.sin(dLng / 2) ** 2;
    return 2 * R * Math.asin(Math.sqrt(x));
  };

  /* Панель накриває частину полотна, тож геометричний центр карти і центр
     ВИДИМОЇ частини — різні точки. Без поправки маркери сходяться під
     панель. Її позиція залежить від брейкпоінта: на десктопі вона злiва
     (ріжемо ліве поле), на мобільному — знизу (ріжемо нижнє). Обидва
     поля міряємо з реальної геометрії, а не константами: ширина панелі
     відносна (min(26rem, 34vw)), а висота залежить від вмісту. */
  const fitPad = () => {
    const BASE = 88, GAP = 24;
    const panel = section.querySelector('.map__panel');
    const pad = { top: BASE, bottom: BASE, left: BASE, right: BASE };
    if (!panel) return pad;

    const canvas = root.getBoundingClientRect();
    const box = panel.getBoundingClientRect();
    if (!box.width || !box.height) return pad;

    /* Запас під тултип: fitBounds рівняє КООРДИНАТИ маркерів, а не DOM
       бульбашки над ними. Тултип висить на активному маркері й виступає
       за нього вбік (TIP_MAX_W) і вгору, тож без цього запасу
       маркер стає рівно за краєм панелі, а його підпис — під панеллю.
       Половина ширини, бо бульбашка центрована над маркером. */
    const TIP_X = TIP_MAX_W / 2, TIP_Y = 88;
    if (matchMedia('(min-width: 1081px)').matches) {
      pad.left = Math.max(BASE, box.right - canvas.left + GAP + TIP_X);
    } else {
      pad.bottom = Math.max(BASE, canvas.bottom - box.top + GAP);
      pad.top = Math.max(BASE, TIP_Y);
    }
    /* Поля не можуть з'їсти все полотно: Mapbox із padding, більшим за
       розмір контейнера, кидає помилку й не рухає камеру взагалі. */
    const maxX = Math.max(BASE, canvas.width / 2 - BASE);
    const maxY = Math.max(BASE, canvas.height / 2 - BASE);
    pad.left = Math.min(pad.left, maxX);
    pad.right = Math.min(pad.right, maxX);
    pad.top = Math.min(pad.top, maxY);
    pad.bottom = Math.min(pad.bottom, maxY);
    return pad;
  };

  const fitAll = () => {
    const b = new mapboxgl.LngLatBounds();
    halls.forEach((h) => b.extend([h.lng, h.lat]));
    if (me) b.extend(me);
    map.fitBounds(b, { padding: fitPad(), duration: dur, maxZoom: 14 });
  };

  /* Клік по карті (не по маркеру) — повний скид вибору: знімаємо
     активний зал, ховаємо тултип, прибираємо намальований маршрут і
     повертаємо огляд усіх трьох локацій. Маршрут гасне сам: після
     active = null drawActiveRoute() кладе в джерело порожню колекцію. */
  const reset = () => {
    if (!ready) return;
    active = null;
    halls.forEach((x) => {
      x.li.classList.remove('is-current');
      x.el?.classList.remove('is-current');
    });
    tipPopup?.remove();
    drawActiveRoute();
    fitAll();
  };

  /* Час доїзду показуємо для ВСІХ залів одразу (не лише обраного) —
     тому маршрут тягнемо трьома паралельними запитами, по одному на
     зал, а не одним для active. Кеш per-зал/per-режим: перемикання
     пішки⇄авто туди-сюди не б'є по Directions API вдруге. */
  const routeUrl = (h) => `https://api.mapbox.com/directions/v5/mapbox/${mode}/${me[0]},${me[1]};${h.lng},${h.lat}`
    + `?geometries=geojson&overview=full&language=${lang.split('-')[0]}&access_token=${mapboxgl.accessToken}`;

  const renderHall = (h) => {
    if (!me) { label(h, null); return; }
    const cache = (h.routes ??= {})[mode];
    if (!cache || cache === 'error') { label(h, txt('straight', { dist: fmtDist(h.dist) })); return; }
    label(h, txt(mode, { dist: fmtDist(cache.distance), time: fmtTime(cache.duration) }));
  };

  const drawActiveRoute = () => {
    if (!ready) return;
    const cache = active && (active.routes ??= {})[mode];
    if (!me || !active || !cache || cache === 'error') {
      map.getSource('route').setData({ type: 'FeatureCollection', features: [] });
      return;
    }
    map.getSource('route').setData({ type: 'Feature', geometry: cache.geometry });
    const b = new mapboxgl.LngLatBounds();
    cache.geometry.coordinates.forEach((c) => b.extend(c));
    map.fitBounds(b, { padding: fitPad(), duration: dur });
  };

  const fetchRoute = (h) => {
    const cache = (h.routes ??= {});
    if (cache[mode] || h.fetching?.[mode]) return;   // вже маємо або вже в польоті
    const fetchingMode = mode;   // режим міг перемкнутись до відповіді — пишемо в правильний кеш
    h.fetching ??= {};
    h.fetching[fetchingMode] = fetch(routeUrl(h))
      .then((res) => res.ok ? res.json() : Promise.reject(res.status))
      .then((data) => {
        const r = data.routes?.[0];
        if (!r) throw new Error('no route');
        cache[fetchingMode] = { distance: r.distance, duration: r.duration, geometry: r.geometry };
      })
      .catch(() => { cache[fetchingMode] = 'error'; })
      .finally(() => {
        delete h.fetching[fetchingMode];
        if (fetchingMode === mode) { renderHall(h); if (h === active) drawActiveRoute(); }
      });
  };
  const fetchAllRoutes = () => { if (me) halls.forEach(fetchRoute); };

  /* Тултип активного залу. Один інстанс на всю карту, який переїжджає за
     активним маркером: так і тултип завжди рівно один, і Mapbox сам
     рахує позицію при зумі/русі — вручну це довелось би тримати
     синхронно з проєкцією.
     closeButton/closeOnClick вимкнені: це підпис до обраного стану, а не
     діалог, який користувач закриває. Прив'язка не .setPopup() на маркері
     (той перемикався б кліком), а явний addTo у pick(). */
  const tip = () => (tipPopup ??= new mapboxgl.Popup({
    closeButton: false,
    closeOnClick: false,
    focusAfterOpen: false,
    /* Зсув ПО КОЖНОМУ якорю, а не однією парою [0,-66]. Mapbox сам
       перекидає бульбашку на інший якір, коли над маркером не вистачає
       місця — а після fitBounds на весь маршрут маркер якраз опиняється
       біля краю вʼюпорта. З однією парою бічний якір отримував той самий
       зсув «на 66 вгору» і бульбашка лізла на краплю збоку.
       Крапля 50px (.map__pin 3.125rem), активна ще й × 1.2 від низу —
       тобто 60px; від цього й рахуємо просвіт для кожного напрямку. */
    offset: {
      bottom: [0, -66], 'bottom-left': [0, -66], 'bottom-right': [0, -66],
      top: [0, 8], 'top-left': [0, 8], 'top-right': [0, 8],
      left: [22, -32], right: [-22, -32],
    },
    maxWidth: `${TIP_MAX_W}px`,   // те саме число, що в запасі fitPad()
    className: 'map__tip',
  }));

  /* setDOMContent, а не setHTML: назву й адресу ми читаємо з розмітки
     через textContent, тобто вже декодованими. Назад у HTML їх довелось
     би екранувати вручну, і «вул. Шота Руставелі, 5 & 7» зламало б
     розмітку. Через textContent екранування не потрібне взагалі. */
  const showTip = (h) => {
    const box = document.createElement('div');
    const name = document.createElement('span');
    name.className = 'map__tip-name';
    name.textContent = h.name;
    box.appendChild(name);
    if (h.address) {
      const addr = document.createElement('span');
      addr.className = 'map__tip-addr';
      addr.textContent = h.address;
      box.appendChild(addr);
    }
    tip().setLngLat([h.lng, h.lat]).setDOMContent(box).addTo(map);
  };

  const pick = (h) => {
    if (!ready) { pending = h; return; }
    active = h;
    halls.forEach((x) => {
      x.li.classList.toggle('is-current', x === h);
      x.el.classList.toggle('is-current', x === h);
    });
    showTip(h);
    if (me) { fetchAllRoutes(); drawActiveRoute(); return; }
    /* padding, а не offset: ця версія Mapbox лишає padding від fitBounds
       на камері, тож offset додавався б ПОВЕРХ уже зсунутого центру —
       і зал на десктопі їхав удвічі правіше, ніж треба. padding же
       задається абсолютно й перезаписує те, що лишилось від fitBounds. */
    map[dur ? 'flyTo' : 'jumpTo']({ center: [h.lng, h.lat], zoom: 14, duration: dur, padding: fitPad() });
    say(txt('hint'));
  };

  const init = () => {
    if (map) return;
    /* is-ready до створення карти: полотно без JS схлопнуте (display:none),
       а Mapbox міряє контейнер у конструкторі — нульовий розмір дав би
       порожню карту до першого resize */
    section.classList.add('is-ready');
    pending = halls.find((h) => h.slug === root.dataset.active) ?? null;
    mapboxgl.accessToken = root.dataset.token;
    map = new mapboxgl.Map({
      container: root,
      /* light-v11 як основа, далі перефарбовуємо шари під палітру (див.
         map.on('load')). dark-v11 не підійшов: його майже чорні будівлі
         й вода — найтемніше, що є на сторінці, а найтемнішим за брендом
         мусить бути мох. */
      style: 'mapbox://styles/mapbox/light-v11',
      center: [24.02, 49.83],
      zoom: 11,
      cooperativeGestures: true,   // колесо скролить сторінку (Lenis), Ctrl/⌘+колесо — зум
      locale: {
        'ScrollZoomBlocker.CtrlMessage': txt('ctrlZoom'),
        'ScrollZoomBlocker.CmdMessage': txt('cmdZoom'),
        'TouchPanBlocker.Message': txt('twoFingers'),
      },
    });
    map.addControl(new mapboxgl.NavigationControl({ showCompass: false }));
    /* Клік по порожній карті — скид вибору. Маркери й попап Mapbox монтує
       в getCanvasContainer(), тобто в той самий контейнер, де слухає
       події карта: клік по маркеру спливає сюди ПІСЛЯ pick() і одразу
       скидав би щойно обраний зал. Тому відсіюємо кліки, що прийшли з
       маркера або з самого тултипа. Фільтр тут, а не stopPropagation на
       кожному маркері: одне місце на всі джерела кліку. */
    map.on('click', (e) => {
      if (e.originalEvent.target.closest('.mapboxgl-marker, .mapboxgl-popup')) return;
      reset();
    });
    map.on('load', () => {
      /* Перефарбування під палітру бренду замість окремого Studio-стилю.
         Ідемо по layers самого стилю і б'ємо по ТИПУ шару, а не по
         іменах: id у Mapbox змінюються між версіями стилю, і хардкод
         мовчки перестав би працювати (getLayer просто не знайшов би шар).
         Стеля палітри — мох #31352A: найтемніше на сторінці мусить бути
         він, а не чорні будівлі Mapbox. Фільтр на canvas не підходить —
         перефарбував би й маркери з маршрутом. */
      const PAINT = {
        land:      '#EAE5DF',   // --clr-page: земля = фон сторінки
        water:     '#CFC8BE',   // на крок темніше за землю, без синяви
        building:  '#DCD5CC',   // забудова читається масою, не плямою
        road:      '#F4F1EC',   // дороги світліші за землю — як на плані
        label:     '#5A5F4C',   // мох, освітлений до читабельного на бежі
        halo:      '#EAE5DF',
      };
      map.getStyle().layers.forEach(({ id, type }) => {
        const key = id.toLowerCase();
        try {
          if (type === 'background') map.setPaintProperty(id, 'background-color', PAINT.land);
          else if (type === 'fill') {
            map.setPaintProperty(id, 'fill-color',
              key.includes('water') ? PAINT.water
              : key.includes('building') ? PAINT.building
              : PAINT.land);
          } else if (type === 'line') {
            map.setPaintProperty(id, 'line-color',
              key.includes('water') ? PAINT.water : PAINT.road);
          } else if (type === 'symbol') {
            map.setPaintProperty(id, 'text-color', PAINT.label);
            map.setPaintProperty(id, 'text-halo-color', PAINT.halo);
          }
        } catch { /* шар не приймає цю властивість — пропускаємо */ }
      });
      map.addSource('route', { type: 'geojson', data: { type: 'FeatureCollection', features: [] } });
      /* Підкладка світліша за землю (#EAE5DF) — інакше після
         перефарбування вона зрівнялась би з нею і зникла; лінія маршруту
         мусить читатись і на землі, і на дорогах. */
      map.addLayer({ id: 'route-casing', type: 'line', source: 'route', layout: { 'line-cap': 'round', 'line-join': 'round' }, paint: { 'line-color': '#FBF9F6', 'line-width': 7 } });
      map.addLayer({ id: 'route', type: 'line', source: 'route', layout: { 'line-cap': 'round', 'line-join': 'round' }, paint: { 'line-color': '#4A4F3C', 'line-width': 3.5 } });
      ready = true;
      if (pending) pick(pending); else fitAll();
      pending = null;
    });
    halls.forEach((h) => {
      const { wrap, el } = markerEl('button', 'map__marker', h.name);
      /* Повторний клік по активному маркеру ховає тултип — і виходить,
         не доходячи до pick(): інакше карта заново летіла б до того
         самого залу й перемальовувала маршрут. Клік по назві в панелі
         лишається звичайним вибором, без перемикання. */
      el.addEventListener('click', () => {
        if (h === active && tipPopup?.isOpen()) { tipPopup.remove(); return; }
        pick(h);
      });
      h.el = el;
      new mapboxgl.Marker({ element: wrap, anchor: 'bottom' }).setLngLat([h.lng, h.lat]).addTo(map);
    });
  };
  /* Ліниво: сам скрипт уже завантажено, але WebGL-полотно й тайли — лише
     коли секція підходить до вʼюпорту (або по першому кліку в панелі) */
  new IntersectionObserver((entries, io) => {
    if (!entries.some((e) => e.isIntersecting)) return;
    init();
    io.disconnect();
  }, { rootMargin: '400px' }).observe(section);

  const locate = () => {
    if (!navigator.geolocation || !isSecureContext) {
      say(txt('noGeo'));
      locateBtn.disabled = true;
      return;
    }
    locateBtn.disabled = true;
    say(txt('locating'));
    navigator.geolocation.getCurrentPosition((pos) => {
      locateBtn.disabled = false;
      me = [pos.coords.longitude, pos.coords.latitude];
      init();
      if (!meMarker) {
        meMarker = new mapboxgl.Marker({ element: markerEl('div', 'map__me').wrap }).setLngLat(me).addTo(map);
      } else meMarker.setLngLat(me);
      // пряма відстань — миттєвий підпис для всіх залів, поки не приїхали
      // реальні маршрути (renderHall сама замінить текст, коли прийдуть)
      let nearest = null;
      halls.forEach((h) => {
        h.dist = haversine(me, [h.lng, h.lat]);
        renderHall(h);
        if (!nearest || h.dist < nearest.dist) nearest = h;
      });
      say('');
      pick(active ?? pending ?? nearest);   // pick() сама тягне маршрути для всіх залів
    }, (err) => {
      locateBtn.disabled = false;
      say(txt(err.code === 1 ? 'denied' : 'failed'));
    }, { timeout: 10000, maximumAge: 300000 });
  };
  locateBtn.addEventListener('click', locate);

  section.addEventListener('click', (e) => {
    const pickBtn = e.target.closest('[data-map-pick]');
    if (pickBtn) {
      init();
      pick(halls.find((x) => x.li === pickBtn.closest('[data-hall]')));
      return;
    }
    const modeBtn = e.target.closest('[data-map-mode]');
    if (!modeBtn || modeBtn.dataset.mapMode === mode) return;
    mode = modeBtn.dataset.mapMode;
    section.querySelectorAll('[data-map-mode]').forEach((b) => {
      const on = b === modeBtn;
      b.classList.toggle('is-current', on);
      b.setAttribute('aria-pressed', on);
    });
    halls.forEach(renderHall);   // одразу кеш нового режиму або пряма відстань, поки не прийшли відповіді
    fetchAllRoutes();
    drawActiveRoute();
  });
})();
