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
  const halls = [...section.querySelectorAll('[data-hall]')].map((li) => ({
    li, slug: li.dataset.slug, lng: +li.dataset.lng, lat: +li.dataset.lat,
    name: li.querySelector('[data-map-pick]').textContent.trim(),
  }));
  /* reduced-motion: карта не летить, а стрибає — духу сторінки це не
     міняє, а людям із вестибулярними проблемами flyTo реально шкодить */
  const dur = matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 900;

  let map = null;
  let ready = false;      // 'load' карти минув — є джерело маршруту, можна fitBounds
  let pending = null;     // зал, обраний до 'load' — pick відкладається на onload
  let me = null;          // [lng, lat] відвідувача, після геолокації
  let meMarker = null;
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
    wrap.appendChild(el);
    return { wrap, el };
  };
  const fmtDist = (m) => m < 1000 ? `${Math.round(m)} м` : `${(m / 1000).toFixed(1).replace('.', ',')} км`;
  const fmtTime = (s) => {
    const h = Math.floor(s / 3600), min = Math.round((s % 3600) / 60);
    return h ? `${h} год ${min} хв` : `${min} хв`;
  };
  /* Пряма відстань — щоб підписати ВСІ зали одразу після геолокації,
     не роблячи три запити в Directions заради «який ближче» */
  const haversine = (a, b) => {
    const R = 6371000, toRad = (d) => d * Math.PI / 180;
    const dLat = toRad(b[1] - a[1]), dLng = toRad(b[0] - a[0]);
    const x = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(a[1])) * Math.cos(toRad(b[1])) * Math.sin(dLng / 2) ** 2;
    return 2 * R * Math.asin(Math.sqrt(x));
  };

  const updateGmaps = () => {
    section.querySelectorAll('[data-map-gmaps]').forEach((a) => {
      const url = new URL(a.href);
      url.searchParams.set('travelmode', mode);
      if (me) url.searchParams.set('origin', `${me[1]},${me[0]}`);
      a.href = url.toString();
    });
  };

  const fitAll = () => {
    const b = new mapboxgl.LngLatBounds();
    halls.forEach((h) => b.extend([h.lng, h.lat]));
    if (me) b.extend(me);
    map.fitBounds(b, { padding: 56, duration: dur, maxZoom: 14 });
  };

  /* Час доїзду показуємо для ВСІХ залів одразу (не лише обраного) —
     тому маршрут тягнемо трьома паралельними запитами, по одному на
     зал, а не одним для active. Кеш per-зал/per-режим: перемикання
     пішки⇄авто туди-сюди не б'є по Directions API вдруге. */
  const routeUrl = (h) => `https://api.mapbox.com/directions/v5/mapbox/${mode}/${me[0]},${me[1]};${h.lng},${h.lat}`
    + `?geometries=geojson&overview=full&language=uk&access_token=${mapboxgl.accessToken}`;

  const renderHall = (h) => {
    if (!me) { label(h, null); return; }
    const cache = (h.routes ??= {})[mode];
    if (!cache || cache === 'error') { label(h, `~${fmtDist(h.dist)} по прямій`); return; }
    label(h, `${fmtDist(cache.distance)} · ${fmtTime(cache.duration)} ${mode === 'walking' ? 'пішки' : 'автом'}`);
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
    map.fitBounds(b, { padding: 56, duration: dur });
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

  const pick = (h) => {
    if (!ready) { pending = h; return; }
    active = h;
    halls.forEach((x) => {
      x.li.classList.toggle('is-current', x === h);
      x.el.classList.toggle('is-current', x === h);
    });
    history.replaceState(null, '', `?loc=${h.slug}`);
    if (me) { fetchAllRoutes(); drawActiveRoute(); return; }
    map[dur ? 'flyTo' : 'jumpTo']({ center: [h.lng, h.lat], zoom: 14, duration: dur });
    say('Натисніть «Де я», щоб побачити маршрут.');
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
      style: 'mapbox://styles/mapbox/light-v11',
      center: [24.02, 49.83],
      zoom: 11,
      cooperativeGestures: true,   // колесо скролить сторінку (Lenis), Ctrl/⌘+колесо — зум
      locale: {
        'ScrollZoomBlocker.CtrlMessage': 'Ctrl + скрол, щоб масштабувати',
        'ScrollZoomBlocker.CmdMessage': '⌘ + скрол, щоб масштабувати',
        'TouchPanBlocker.Message': 'Рухайте карту двома пальцями',
      },
    });
    map.addControl(new mapboxgl.NavigationControl({ showCompass: false }));
    map.on('load', () => {
      /* Без окремого Studio-стилю: підмінюємо лише землю й воду під беж
         сторінки — решта (дороги, підписи) з light-v11 читається на ньому
         нормально. Фільтр на canvas не підходить — перефарбував би й маркери. */
      if (map.getLayer('land'))  map.setPaintProperty('land',  'background-color', '#EAE5DF');
      if (map.getLayer('water')) map.setPaintProperty('water', 'fill-color', '#D8D2C9');
      map.addSource('route', { type: 'geojson', data: { type: 'FeatureCollection', features: [] } });
      // світла підкладка під лінією — щоб мох читався і поверх темних доріг
      map.addLayer({ id: 'route-casing', type: 'line', source: 'route', layout: { 'line-cap': 'round', 'line-join': 'round' }, paint: { 'line-color': '#EAE5DF', 'line-width': 7 } });
      map.addLayer({ id: 'route', type: 'line', source: 'route', layout: { 'line-cap': 'round', 'line-join': 'round' }, paint: { 'line-color': '#4A4F3C', 'line-width': 3.5 } });
      ready = true;
      if (pending) pick(pending); else fitAll();
      pending = null;
    });
    halls.forEach((h) => {
      const { wrap, el } = markerEl('button', 'map__marker', h.name);
      el.addEventListener('click', () => pick(h));
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
      say('Геолокація недоступна — потрібен https.');
      locateBtn.disabled = true;
      return;
    }
    locateBtn.disabled = true;
    say('Визначаємо…');
    navigator.geolocation.getCurrentPosition((pos) => {
      locateBtn.disabled = false;
      me = [pos.coords.longitude, pos.coords.latitude];
      init();
      if (!meMarker) {
        meMarker = new mapboxgl.Marker({ element: markerEl('div', 'map__me').wrap }).setLngLat(me).addTo(map);
      } else meMarker.setLngLat(me);
      updateGmaps();
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
      say(err.code === 1
        ? 'Доступ до геолокації заборонено — маршрут покажемо в Google Maps.'
        : 'Не вдалося визначити позицію, спробуйте ще раз.');
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
    updateGmaps();
    halls.forEach(renderHall);   // одразу кеш нового режиму або пряма відстань, поки не прийшли відповіді
    fetchAllRoutes();
    drawActiveRoute();
  });
})();
