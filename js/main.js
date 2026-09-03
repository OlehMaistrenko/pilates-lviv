/* ============================================================
   Reveal · motion (Lenis + GSAP) · header state · fullscreen menu
   ============================================================ */
(() => {
  'use strict';

  // жива-посилання на активний Lenis (лише десктоп, див. Motion нижче) —
  // якір-скрол використовує її lerp-easing замість нативного CSS smooth
  let lenisInstance = null;
  // спільний namespace для функцій, потрібних поза блоком, що їх визначає
  // (напр. Tabs викликає loadModal, призначену в блоці Modal overlay
  // нижче) — window, не змінна модуля, щоб лишався доступний, якщо цей
  // файл колись розіб'ють на кілька <script>.
  window._functions = window._functions || {};

  /* ---- Reveal "lines" — авто-врап тексту в .word-mask по словах ------ */
  (() => {
    /* <br> лишається в розмітці як реальний розрив. Кожне слово — окрема
       .word-mask (не .line: маска тепер по слову, не по рядку). Ручна
       розмітка (як у hero__title, де розрив не по словах) лишається
       валідною — авто-врап її не чіпає. */
    document.querySelectorAll('[data-reveal="lines"]').forEach((el) => {
      if (el.querySelector('.word-mask')) return;
      const html = [];
      let i = 0;
      el.childNodes.forEach((node) => {
        if (node.nodeName === 'BR') { html.push('<br>'); return; }
        /* Інлайн-теги всередині заголовка (<em> для акцентного слова) треба
           перенести на кожне слово: innerHTML нижче переписує вміст цілком,
           і без цього тег просто зникав би разом із акцентом.
           Вкладеність глибше одного рівня схлопується — заголовку вистачає. */
        const tag = node.nodeType === 1 ? node.tagName.toLowerCase() : '';
        const attrs = tag ? [...node.attributes].map((a) => ` ${a.name}="${a.value}"`).join('') : '';
        const open = tag ? `<${tag}${attrs}>` : '';
        const close = tag ? `</${tag}>` : '';
        (node.textContent || '').split(/\s+/).filter(Boolean).forEach((word) => {
          html.push(`<span class="word-mask" style="--word-i: ${i++}"><span>${open}${word}${close}</span></span>`);
        });
      });
      el.innerHTML = html.join(' ');
    });
  })();

  /* ---- Reveal on scroll --------------------------------------------- */
  (() => {
    /* Лічильник для [data-count] (напр. "500+ клієнтів") — рахує вгору до
       значення, вже надрукованого в розмітці. Запускається разом з
       .is-visible своєї картки, --reveal-i (успадковується як CSS custom
       property) дає той самий стагер, що й решта reveal-ів.
       min-width фіксує виміряну фінальну ширину — шрифт проєкту може не
       мати tabular-nums, покладатись на font-variant-numeric не можна.
       Останній кадр повертає оригінальний текст дослівно, без ризику
       розбіжності з toLocaleString. */
    const animateCounts = (root) => {
      root.querySelectorAll('[data-count]').forEach((el) => {
        const orig = el.textContent;
        const digits = orig.match(/[\d\s]+/)?.[0];
        const target = digits && parseInt(digits.replace(/\s/g, ''), 10);
        if (!target) return;
        el.style.minWidth = el.offsetWidth + 'px';
        const delay = (parseFloat(getComputedStyle(el).getPropertyValue('--reveal-i')) || 0) * 140;
        const dur = 900;
        setTimeout(() => {
          const start = performance.now();
          const tick = (now) => {
            const p = Math.min((now - start) / dur, 1);
            const eased = 1 - (1 - p) ** 3;
            if (p < 1) {
              el.textContent = orig.replace(digits, Math.round(target * eased).toLocaleString('uk-UA'));
              requestAnimationFrame(tick);
            } else {
              el.textContent = orig;
              el.style.minWidth = '';
            }
          };
          requestAnimationFrame(tick);
        }, delay);
      });
    };

    const els = document.querySelectorAll('[data-reveal]');
    if (!els.length) return;
    if (!('IntersectionObserver' in window)) {
      els.forEach((el) => el.classList.add('is-visible'));
      return;
    }
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (!e.isIntersecting) return;
        e.target.classList.add('is-visible');
        animateCounts(e.target);
        io.unobserve(e.target);
      });
    }, { rootMargin: '0px 0px -50px 0px', threshold: 0 });
    els.forEach((el) => {
      /* рефреш не з нуля: те, що вже прокручене вище екрана, спостерігач
         не покаже ніколи — показуємо одразу, без анімації */
      if (el.getBoundingClientRect().bottom < 0) {
        el.classList.add('is-visible');
        return;
      }
      io.observe(el);
    });
  })();

  /* ---- Motion: Lenis smooth scroll + GSAP ScrollTrigger --------------
     Прості reveal-и лишаються на IntersectionObserver вище (дешевше й
     краще для PageSpeed). Сюди йде лише те, чого спостерігач не вміє:
     scrub (привʼязка до позиції скролу) і pin. Вендори підключає лише
     сторінка, що виставила $vendor_motion перед include footer.php. */
  (() => {
    if (typeof window.Lenis === 'undefined' || typeof window.gsap === 'undefined') return;
    gsap.registerPlugin(ScrollTrigger);

    /* Один інстанс Lenis на життя сторінки, свідомо поза matchMedia: якщо
       створювати/знищувати його там, GSAP-послідовність revert/onMatch
       скидає базову лінію скролу, і scrub-твіни після resize зʼїжджають
       (zoom-in застрягає на scale(.85)). Гейта по ширині немає — Lenis v1
       не перехоплює touch (syncTouch:false), тож мобілці не шкодить. */
    /* [data-lenis-prevent-zoom] (карта) — глушить Lenis лише при затиснутому
       Ctrl/⌘ (зум карти, Mapbox cooperativeGestures); без модифікатора
       сторінка скролиться як усюди. Capture-слухач ловить модифікатор
       раніше, ніж Lenis обробить wheel. */
    let zoomMod = false;
    addEventListener('wheel', (e) => { zoomMod = e.ctrlKey || e.metaKey; }, { capture: true, passive: true });

    const lenis = new Lenis({
      lerp: 0.1,
      smoothWheel: true,
      prevent: (node) => zoomMod && node.hasAttribute?.('data-lenis-prevent-zoom'),
    });
    lenisInstance = lenis;              // якір-скрол, див. блок нижче
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((t) => lenis.raf(t * 1000));
    gsap.ticker.lagSmoothing(0);
        /* matchMedia, не одноразовий desktop()-чек: інакше при resize через
       1081px (згорнути/розгорнути devtools, повернути планшет) нічого не
       переініціалізується — pin/zoom-секції так і лишаються в стані старої
       ширини (напр. [data-anim="zoom-in"] — CSS-стартовий scale(.85) на
       десктопі, main.css — залишається стиснутим без твіна, що поверне
       scale(1)). matchMedia сам revert-ить твіни/ScrollTrigger-и й
       інлайн-стилі при виході з умови і наново створює їх при вході —
       без цього довелось би вручну відстежувати кожен tween/trigger на
       resize. */
    

    /* Рефреш не з нульової позиції: браузер відновлює скрол асинхронно —
       після defer-скриптів, шрифтів і завантаження зображень, коли висота
       сторінки ще мінялась (і при відкритті з відновленою/якірною позицією
       скролу). Тригери, пораховані до цього, лишаються зміщеними — тож
       перераховуємо їх, коли сторінка вже має фінальну висоту. */
    document.fonts?.ready.then(() => ScrollTrigger.refresh());
    window.addEventListener('load', () => ScrollTrigger.refresh());
  })();

  /* ---- Accordion: div-и, стан у класі .accordion--open, клік по всій
     картці, плавна анімація .accordion__body через max-height. --------- */
  (() => {
    const DURATION = 350;
    /* accordion__body росте/стискається (max-height), тож усе нижче на
       сторінці зсувається — pin-треки (grants.php: "Як подати заявку")
       кешують позиції на останньому refresh(), без нового вони лишаються
       зміщеними на висоту акордеона. ScrollTrigger може бути не завантажений
       (акордеон живе поза Motion-IIFE, є й на сторінках без $vendor_motion). */
    const refreshScroll = () => {
      if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
    };

    function animateOpen(item, body, summary) {
      item.classList.add('accordion--open');
      summary.setAttribute('aria-expanded', 'true');
      const margin = getComputedStyle(body).marginTop;
      const target = body.scrollHeight;
      body.animate(
        { maxHeight: ['0px', `${target}px`], marginTop: ['0px', margin] },
        { duration: DURATION, easing: 'ease' }
      ).finished.then(refreshScroll).catch(() => {});
    }

    function animateClose(item, body, summary) {
      const margin = getComputedStyle(body).marginTop;
      const from = body.scrollHeight;
      summary.setAttribute('aria-expanded', 'false');
      body.animate(
        { maxHeight: [`${from}px`, '0px'], marginTop: [margin, '0px'] },
        { duration: DURATION, easing: 'ease' }
      ).finished.then(() => {
        item.classList.remove('accordion--open');
        refreshScroll();
      }).catch(() => {});
    }

    document.querySelectorAll('.accordion').forEach((item) => {
      const body = item.querySelector(':scope > .accordion__body');
      const summary = item.querySelector(':scope > .accordion__summary');
      if (!body || !summary) return;

      function toggle() {
        if (item.classList.contains('accordion--open')) {
          animateClose(item, body, summary);
          return;
        }
        const group = item.dataset.accordionGroup;
        if (group) {
          document.querySelectorAll(`.accordion--open[data-accordion-group="${group}"]`).forEach((other) => {
            if (other !== item) {
              animateClose(other, other.querySelector(':scope > .accordion__body'), other.querySelector(':scope > .accordion__summary'));
            }
          });
        }
        animateOpen(item, body, summary);
      }

      item.addEventListener('click', toggle);
      summary.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        e.preventDefault();
        toggle();
      });
    });
  })();

  /* ---- Swiper: спільний ініціалізатор (vendor підключає лише сторінка,
     що виставила $vendor_swiper перед include footer.php) ------------- */
  (() => {
    const SWIPER_DEFAULTS = {
      slidesPerView: 'auto',
      speed: 500,
      watchOverflow: true,   // мало слайдів → swiper-locked, контроли гаснуть
      pagination: { clickable: true },
    };

    function initSwiper(el) {
      if (!el || typeof window.Swiper === 'undefined') return null;

      const overrides = JSON.parse(el.dataset.swiper || '{}');
      const totalSlides = el.querySelectorAll('.swiper-slide').length;
      const wrap = el.closest('.swiper-wrap') ?? el.parentElement;

      const paginationBase = {
        ...SWIPER_DEFAULTS.pagination,
        el: wrap.querySelector('.swiper-pagination') ?? null,
      };
      if (totalSlides > 7) paginationBase.dynamicBullets = true;

      const options = {
        ...SWIPER_DEFAULTS,
        ...overrides,
        pagination: { ...paginationBase, ...(overrides.pagination ?? {}) },
        navigation: {
          prevEl: wrap.querySelector('.swiper-prev') ?? null,
          nextEl: wrap.querySelector('.swiper-next') ?? null,
          ...(overrides.navigation ?? {}),
        },
      };
      return new Swiper(el, options);
    }

    document.querySelectorAll('.swiper').forEach(initSwiper);
  })();

  /* ---- In-page anchor scroll: Lenis lerp-easing (той самий плавний рух,
     що й решта сторінки). Lenis тепер один на всі ширини, тож і якір іде
     через нього; нативний smooth-scroll (html{scroll-behavior:smooth},
     main.css) лишається фолбеком там, де Lenis не піднявся взагалі —
     сторінка без $vendor_motion. -------------------- */
  (() => {
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a[href^="#"]');
      if (!link || !lenisInstance) return;
      const id = link.getAttribute('href').slice(1);
      const target = id && document.getElementById(id);
      if (!target) return;
      e.preventDefault();
      lenisInstance.scrollTo(target, { duration: 2.4, easing: (t) => 1 - Math.pow(1 - t, 4) });
    });
  })();

 

  /* ---- Header state: .is-scrolled на <body> --------------------------
     Прозорий хедер над героєм мусить стати непрозорим, щойно під ним
     починається світлий контент. Поріг 8px, а не 0: на iOS bounce-скрол
     дає дрібні від'ємні значення, і клас блимав би на кожен дотик. */
  (() => {
    let ticking = false;
    const apply = () => {
      document.body.classList.toggle('is-scrolled', window.scrollY > 8);
      ticking = false;
    };
    addEventListener('scroll', () => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(apply);
    }, { passive: true });
    apply();   // перезавантаження посеред сторінки зберігає позицію скролу
  })();

  /* ---- Fullscreen menu — body.menu-open перемикає хедер у світлий стан
     (css/main.css, Header) ---------------------------------------------- */
  (() => {
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.getElementById('menu');
    if (!toggle || !menu) return;

    const open = () => {
      menu.hidden = false;
      requestAnimationFrame(() => menu.classList.add('is-open'));
      toggle.setAttribute('aria-expanded', 'true');
      document.body.classList.add('menu-open');
      document.body.style.overflow = 'hidden';
    };
    const close = () => {
      menu.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('menu-open');
      document.body.style.overflow = '';
      // transitionend спливає і з панелі, і з кнопок усередині — реагуємо
      // лише на власний перехід кореня, інакше меню ховається завчасно
      const done = (e) => {
        if (e.target !== menu) return;
        menu.hidden = true;
        menu.removeEventListener('transitionend', done);
      };
      menu.addEventListener('transitionend', done);
    };

    toggle.addEventListener('click', () =>
      toggle.getAttribute('aria-expanded') === 'true' ? close() : open());
    menu.addEventListener('click', (e) => { if (e.target.closest('a')) close(); });
    addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') close();
    });
  })();

  /* ---- Modal overlay: shared AJAX loader for partials/modals/* -------- */
  (() => {
    const overlay = document.getElementById('modal-overlay');
    const content = document.getElementById('modal-overlay-content');
    if (!overlay || !content) return;

    let trigger = null;

    const open = () => {
      overlay.hidden = false;
      requestAnimationFrame(() => {
        overlay.classList.add('is-open');
        overlay.querySelector('.modal-overlay__close')?.focus();
      });
      document.body.style.overflow = 'hidden';
    };
    const close = () => {
      overlay.classList.remove('is-open');
      document.body.style.overflow = '';
      // те саме, що й у мобільного меню: transitionend спливає з панелі
      // та з елементів форми всередині, слухаємо тільки корінь
      const done = (e) => {
        if (e.target !== overlay) return;
        overlay.hidden = true;
        content.innerHTML = '';
        overlay.removeEventListener('transitionend', done);
      };
      overlay.addEventListener('transitionend', done);
      trigger?.focus();
    };
    // params — рядок "id=2" або URLSearchParams; довільні ключі просто
    // прокидуються в query string AJAX-запиту й стають доступні фрагменту як $_GET
    const load = async (name, params = '') => {
      const qs = new URLSearchParams(params);
      qs.set('name', name);
      try {
        const res = await fetch(`partials/modals/loader.php?${qs}`);
        if (!res.ok) return;
        content.innerHTML = await res.text();
        open();
      } catch { /* тихо ігноруємо — href лишається робочим fallback-ом */ }
    };

    // той самий AJAX-шлях, що [data-modal]-кліки, але викликаний програмно
    // (напр. Tabs — success-попап після сабміту форми теми, не по кліку
    // на посилання з href)
    window._functions.loadModal = (name, params = '') => { trigger = null; return load(name, params); };

    document.addEventListener('click', (e) => {
      const el = e.target.closest('[data-modal]');
      if (!el) return;
      trigger = el;
      // data-modal="career?way=resume" — усе в одному атрибуті, тригер
      // тепер <button> без href; JS ділить на name і query-параметри
      const [name, query = ''] = el.dataset.modal.split('?');
      load(name, query);
    });
    overlay.addEventListener('click', (e) => { if (e.target.closest('[data-modal-close]')) close(); });
    addEventListener('keydown', (e) => { if (e.key === 'Escape' && !overlay.hidden) close(); });

    // ?modal=name&... — відкриває модалку одразу при завантаженні сторінки,
    // усі інші параметри query string летять разом з нею
    const pageParams = new URLSearchParams(location.search);
    const requested = pageParams.get('modal');
    if (requested) load(requested, pageParams);
  })();



  /* ---- Tabs: перемикання панелей послідовним crossfade — спочатку fade-out
     старої, і лише в колбеку (transitionend) fade-in нової, як у референсі
     (zatara-shop product tabs: $tabs.eq(i).siblings(':visible').fadeOut(()=>
     fadeIn) — jQuery fadeOut/fadeIn послідовно, не паралельно). Тут те саме
     на чистому CSS-transition + transitionend, без jQuery і без WAAPI (яка
     раніше давала race: скасований твін → .finished reject → hidden ніколи
     не ставився). Контракт через data-атрибути:
       [data-tabs]                — тумблер (role=tablist), містить кнопки
       [data-tab-btn="slug"]      — кнопка теми
       [data-tabs-viewport]       — обгортка навколо панелей
       [data-tab-panel="slug"]    — сама панель
     Усі панелі лежать у DOM одразу, перша видима, решта — [hidden] у
     розмітці (no-JS fallback без стрибка: без JS видно першу тему повністю
     робочою формою). JS далі сам ставить/знімає hidden і disabled —
     required на невидимому інпуті інакше мовчки блокує сабміт "не тієї"
     форми. */
  function initTabs(root) {
    const buttons = [...root.querySelectorAll('[data-tab-btn]')];
    const viewport = root.querySelector('[data-tabs-viewport]');
    if (!buttons.length || !viewport) return;
    const panels = [...viewport.querySelectorAll('[data-tab-panel]')];
    if (!panels.length) return;

    const setDisabled = (panel, off) => {
      panel.querySelectorAll('input, select, textarea').forEach((f) => { f.disabled = off; });
    };

    let current = panels.find((p) => !p.hidden) || null;
    let pending = null; // slug, на який чекаємо після fade-out поточної

    // висота секції змінюється (нова панель має інший набір полів) — усе,
    // що пінилось/скрабилось нижче на сторінці, лишається зі старими
    // позиціями без рефрешу (той самий refreshScroll, що в Accordion вище)
    const refreshScroll = () => {
      if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
    };

    const fadeIn = (panel) => {
      panel.hidden = false;
      setDisabled(panel, false);
      // подвійний rAF, не одинарний: викликається з transitionend-колбека
      // fade-out, тобто вже всередині кадру рендеру — один rAF потрапляє
      // в той самий кадр, що й зняття [hidden], і браузер знову склеює
      // обидві зміни стилю в один recalculation без анімації входу
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          panel.classList.remove('is-hidden');
          refreshScroll();
        });
      });
      current = panel;
      pending = null;
    };

    const select = (slug) => {
      if (slug === pending || (!pending && slug === current?.dataset.tabPanel)) return;
      pending = slug;

      buttons.forEach((b) => {
        const on = b.dataset.tabBtn === slug;
        b.classList.toggle('topic--active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
      });

      const next = panels.find((p) => p.dataset.tabPanel === slug);
      if (!next) return;

      if (!current) {
        if (current) { current.hidden = true; setDisabled(current, true); }
        fadeIn(next);
        return;
      }

      const leaving = current;
      leaving.classList.add('is-hidden');
      leaving.addEventListener('transitionend', function done(e) {
        if (e.target !== leaving) return;           // не ловимо transition дітей
        leaving.removeEventListener('transitionend', done);
        leaving.hidden = true;
        setDisabled(leaving, true);
        // pending міг устигнути змінитись повторним кліком, поки йшов fade-out —
        // показуємо саме той slug, на якому користувач зупинився насправді
        fadeIn(panels.find((p) => p.dataset.tabPanel === pending) ?? next);
      });
    };

    buttons.forEach((b) => b.addEventListener('click', () => select(b.dataset.tabBtn)));
    // is-hidden одразу, не лише hidden: без нього перший показ панелі (яка
    // ще ніколи не була "leaving") не має з чого анімувати вхід — fadeIn
    // знімає клас, якого й так нема, і панель стрибає в кінцевий стан різко
    panels.forEach((p) => { if (p !== current) { p.hidden = true; p.classList.add('is-hidden'); setDisabled(p, true); } });
    if (current) current.classList.remove('is-hidden');
  }

  document.querySelectorAll('[data-tabs]').forEach(initTabs);

})();
