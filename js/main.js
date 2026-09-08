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
       .word-mask (не .line: стагер по слову, не по рядку); показує його
       фейд+зсув у main.css, тому обгортка одна, без вкладеного спана.
       Ручна розмітка (як у hero__title, де розрив не по словах) лишається
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
          html.push(`<span class="word-mask" style="--word-i: ${i++}">${open}${word}${close}</span>`);
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
     scrub (привʼязка до позиції скролу) і pin. Вендори підключає
     footer.php на всіх сторінках (defer); typeof-гейт нижче лишається на
     випадок, якщо скрипт не довантажився. */
  (() => {
    if (typeof window.Lenis === 'undefined' || typeof window.gsap === 'undefined') return;
    gsap.registerPlugin(ScrollTrigger);

    /* Один інстанс Lenis на життя сторінки, свідомо поза matchMedia: якщо
       створювати/знищувати його там, GSAP-послідовність revert/onMatch
       скидає базову лінію скролу, і scrub-твіни після resize зʼїжджають
       (одометр застрягає на проміжній цифрі). Гейта по ширині немає — Lenis v1
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
      // кастомний prevent перебиває дефолтний Lenis-пошук data-lenis-prevent,
      // тож він мусить сам його перевірити (closest — модалка/меню
      // скроляться нативно, без lerp Lenis) поверх зум-гейта карти
      prevent: (node) => node.closest?.('[data-lenis-prevent]') || (zoomMod && node.hasAttribute?.('data-lenis-prevent-zoom')),
    });
    lenisInstance = lenis;              // якір-скрол, див. блок нижче
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((t) => lenis.raf(t * 1000));
    gsap.ticker.lagSmoothing(0);

    /* Одометр і фокус живуть лише на десктопі (≥901px): на вужчих екранах
       CSS ховає одометр і показує цифру в кожному кроці, твінів там не
       треба. matchMedia, а не одноразовий чек: при resize через брейкпоінт
       він сам revert-ить твіни/тригери й інлайн-стилі та створює їх
       наново — без цього колонка застрягла б зі старим зсувом. */
    gsap.matchMedia().add('(min-width: 901px)', () => {
      /* data-anim="pin" — елемент стоїть на місці, поки повз нього проходить
         увесь трек із data-pin-track. Не position:sticky: sticky рахує
         відступ від верху, а одометр перемикає цифру по center center кроку —
         цифра мінялась не там, куди дивиться око.
         start 'center center' по самій цифрі: пін ловить її рівно тоді, коли
         вона доходить до середини екрана, і там і тримає. Тому CSS не
         мусить центрувати її сам — 100vh-бокс, що це робив, лишав цифру
         пів екрана нижче, поки секція ще не доскролила до верху.
         end рахується від низу треку до НИЗУ цифри (див. нижче): вона
         приморожена центром, тож звисає на пів висоти нижче середини.
         Створюємо ДО одометра, щоб ScrollTrigger порахував pin раніше за
         scrub-твін усередині того самого треку. */
      document.querySelectorAll('[data-anim="pin"]').forEach((el) => {
        const track = document.querySelector(el.dataset.pinTrack);
        if (!track) return;
        ScrollTrigger.create({
          trigger: el,
          start: 'center center',
          endTrigger: track,
          /* Цифра приморожена центром на середині екрана, тож її низ звисає
             на пів своєї висоти нижче. Відпускати треба тоді, коли низ треку
             дійде саме до цього низу, — інакше цифра ще стоїть на місці,
             поки наступна секція вже наїхала, і вони накладаються.
             Функція, а не рядок: висота цифри залежить від vw (26vw). */
          end: () => `bottom ${window.innerHeight / 2 + el.offsetHeight / 2}px`,
          pin: el,
          pinSpacing: false,   // трек уже має власну висоту; spacer додав би порожній екран
        });
      });

      /* data-anim="odometer" — стовпчик із N цифр у масці висотою в одну
         (CSS); скрол зсуває його на N-1 позицій уздовж списку з
         data-odometer-for — від центру першого кроку до центру останнього,
         щоб ціла цифра стояла рівно тоді, коли її крок посередині екрана. */
      document.querySelectorAll('[data-anim="odometer"]').forEach((el) => {
        const items = document.querySelector(el.dataset.odometerFor)?.children;
        if (!items || items.length < 2) return;
        gsap.to(el, {
          yPercent: -100 * (items.length - 1) / items.length,
          ease: 'none',
          scrollTrigger: {
            trigger: items[0], start: 'center center',
            endTrigger: items[items.length - 1], end: 'center center',
            scrub: true,
          },
        });
      });

      /* data-anim="focus" — блок у повний колір, поки проходить центр
         екрана; вище й нижче — приглушений */
      document.querySelectorAll('[data-anim="focus"]').forEach((el) => {
        gsap.timeline({ scrollTrigger: { trigger: el, start: 'top 75%', end: 'bottom 25%', scrub: true } })
          .fromTo(el, { opacity: 0.25 }, { opacity: 1, ease: 'none' })
          .to(el, { opacity: 0.25, ease: 'none' });
      });

      /* data-anim="parallax" — шар їде повільніше за скрол. Силу (у % власної
         висоти) задає data-parallax, тож два шари в одній секції з різними
         значеннями дають глибину. Тригер — секція, а не сам шар: інакше
         кожен шар рахував би свій відрізок і вони роз'їхались би. */
      document.querySelectorAll('[data-anim="parallax"]').forEach((el) => {
        const d = parseFloat(el.dataset.parallax) || 8;
        gsap.fromTo(el, { yPercent: -d }, {
          yPercent: d, ease: 'none',
          scrollTrigger: { trigger: el.closest('section'), start: 'top bottom', end: 'bottom top', scrub: true },
        });
      });
    });

    /* data-anim="locations" — секція пінується на N екранів; scrub-таймлайн
       міняє фон жалюзі (смуги нового кадру розкриваються scaleY зі стагером)
       і панель в арці. Не pin+odometer, як у path: там один безперервний
       зсув однієї стрічки, тут — дискретна заміна вмісту, тож кроки
       тримає сам таймлайн, а не окремий твін.
       Поза matchMedia вище навмисно: той гейт — лише десктоп, а тут ефект
       мусить працювати на всіх ширинах. */
    document.querySelectorAll('[data-anim="locations"]').forEach((sec) => {
      const bgs = sec.querySelectorAll('.locations__bg');
      const panels = sec.querySelectorAll('.locations__panel');
      if (panels.length < 2) return;
      // висоту секції (кількість екранів на прокрут) рахує CSS із цього числа
      sec.style.setProperty('--steps', panels.length);

      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: sec,
          start: 'top top',
          end: 'bottom bottom',
          pin: '.locations__stage',
          pinSpacing: false,   // висоту вже дає сама секція (calc зі --steps)
          scrub: true,
        },
      });

      /* Маска жалюзі: SLATS однакових смуг, у кожній видима частка росте
         від 0 до 1. Пишемо градієнт у CSS-змінну щоразу, бо CSS не вміє
         нагенерувати N колірних стопів сам.
         Хвиля: смуга s відкривається не разом з усіма, а у своєму вікні
         прогресу — [s * крок; s * крок + SPAN]. WAVE — яку частку
         загального прогресу з'їдає розбіг між першою й останньою смугою;
         решта (SPAN) лишається на саме розкриття однієї смуги. */
      const SLATS = 30;
      const WAVE = 0.55;
      const SPAN = 1 - WAVE;
      const band = 100 / SLATS;
      const setMask = (el, p) => {
        let stops = '';
        for (let s = 0; s < SLATS; s++) {
          const a = s * band;
          /* 0deg — градієнт іде знизу вгору, тож смуга 0 найнижча.
             Хвиля має котитись згори вниз, тому першою відкривається
             остання смуга: затримка росте від кінця до початку. */
          const delay = s / (SLATS - 1) * WAVE;
          const open = Math.min(1, Math.max(0, (p - delay) / SPAN));
          const cut = (a + band * open).toFixed(3);
          stops += `${s ? ', ' : ''}black ${a.toFixed(3)}% ${cut}%, transparent ${cut}% ${(a + band).toFixed(3)}%`;
        }
        el.style.setProperty('--mask-gradient', `linear-gradient(0deg, ${stops})`);
      };
      bgs.forEach((bg, i) => { if (i) setMask(bg, 0); });

      /* Крок — рівно 1 умовна секунда таймлайна, щоб кожен зал займав
         однакову частку скролу (на цьому тримається поріг зміни панелі).
         До скролу привʼязані ЛИШЕ жалюзі: кадр тягнеться разом із рухом
         пальця. Твінимо проксі-обʼєкт: --open у масці не анімується сам,
         градієнт треба перезбирати щокадру. */
      panels.forEach((panel, i) => {
        if (!i) return;
        const m = { open: 0 };
        tl.to(m, {
          open: 1, ease: 'none', duration: 0.75,
          onUpdate: () => setMask(bgs[i], m.open),
        }, i - 1);
      });

      /* Повільний наїзд кадру на весь пін — один твін на всі зали, а не
         по одному на крок: інакше на межі кроків масштаб стрибав би назад
         на 1. Тягнеться зі скролом (той самий scrub, що й жалюзі).
         Ціль — спільний .locations__bgs, а не кожен .locations__bg: у
         момент переходу два кадри видно одночасно (один крізь маску
         іншого), і на різних масштабах шов між ними був би помітний. */
      tl.fromTo(sec.querySelector('.locations__bgs'),
        { scale: 1 }, { scale: 1.15, ease: 'none', duration: panels.length - 1 }, 0);

      /* Контент в арці зі скролом НЕ звʼязаний: інакше на будь-якій
         проміжній позиції текст завмирає напівпрозорим (і читається як
         баг, і ловить кліки). Тому — звичайна анімація фіксованої
         тривалості, яку запускає перетин порога кроку. */
      let shown = 0;
      const swapTo = (n) => {
        if (n === shown) return;
        const from = panels[shown], to = panels[n];
        shown = n;
        /* Клас ставимо одразу всім, а не в onComplete: при швидкому
           скролі туди-назад killTweensOf обриває твін, і onComplete не
           настав би — панель лишилась би .is-active назавжди. */
        panels.forEach((p, k) => p.classList.toggle('is-active', k === n));
        gsap.killTweensOf(panels);
        /* Перестрибнуту панель (швидкий скрол 1→3) гасимо без анімації —
           інакше на ній лишиться інлайнова opacity від обірваного твіна */
        panels.forEach((p, k) => {
          if (k !== n && p !== from) gsap.set(p, { opacity: 0, yPercent: 0 });
        });
        /* Кросфейд із перекриттям: новий текст рушає ще поки старий гасне
           (delay менший за тривалість виходу) — без паузи з порожньою
           аркою посередині. Зсув малий: на великому тексті довга дорога
           читається як стрибок, а не як плавність. */
        gsap.to(from, { opacity: 0, yPercent: -3, duration: 0.7, ease: 'power2.inOut' });
        gsap.fromTo(to, { opacity: 0, yPercent: 3 },
          { opacity: 1, yPercent: 0, duration: 0.9, ease: 'power2.out', delay: 0.25 });
      };
      /* Твіни останнього кроку закінчуються трохи раніше за рівну довжину —
         порожній твін у кінці добиває таймлайн до цілого числа кроків.
         totalDuration() тут не годиться: він розтягнув би самі твіни, а
         нам треба рівні кроки — на них тримається поріг зміни панелі. */
      tl.to({}, { duration: 0 }, panels.length - 1);
      /* Індекс беремо з прогресу таймлайна, а не .set() на кроці: set при
         скролі вгору лишає останнє значення, а прогрес однаково правильний
         в обидва боки. Поріг — 0.45 кроку: контент арки міняється ще
         посеред розкриття жалюзі, не чекаючи, поки кадр майже готовий. */
      tl.eventCallback('onUpdate', () => {
        swapTo(Math.min(panels.length - 1,
          Math.floor(tl.progress() * (panels.length - 1) + 0.55)));
      });
    });

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
       (акордеон живе поза Motion-IIFE і не залежить від вендорів). */
    const refreshScroll = () => {
      if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
    };

    /* fill:'forwards' обов'язковий — інакше після завершення анімації
       елемент повертається до CSS max-height:0 і відкритий акордеон
       схлопується. Попередню анімацію скасовуємо, щоб швидкі перемикання
       не боролися між собою; from читаємо з поточної висоти, а не зі
       scrollHeight, щоб закриття посеред відкриття не стрибало. */
    function animate(body, from, to, margin) {
      body.__anim?.cancel();
      const a = body.animate(
        { maxHeight: [`${from}px`, `${to}px`], marginTop: margin },
        { duration: DURATION, easing: 'ease', fill: 'forwards' }
      );
      body.__anim = a;
      return a;
    }

    function animateOpen(item, body, summary) {
      /* висоти читаємо ДО додавання .accordion--open: клас знімає max-height,
         після нього from дорівнював би to і анімації не було б видно */
      const from = body.getBoundingClientRect().height;
      const to = body.scrollHeight;
      item.classList.add('accordion--open');
      summary.setAttribute('aria-expanded', 'true');
      const margin = getComputedStyle(body).marginTop;
      const a = animate(body, from, to, ['0px', margin]);
      a.finished.then(() => {
        /* знімаємо pixel-лок: далі висоту тримає .accordion--open{max-height:none} */
        if (body.__anim === a) { a.cancel(); body.__anim = null; }
        refreshScroll();
      }).catch(() => {});
    }

    function animateClose(item, body, summary) {
      const margin = getComputedStyle(body).marginTop;
      summary.setAttribute('aria-expanded', 'false');
      animate(body, body.getBoundingClientRect().height, 0, [margin, '0px'])
        .finished.then(() => {
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

  /* ---- Footer SEO-текст: читати більше/менше ------------------------- */
  (() => {
    const toggle = document.querySelector('.footer-seo__toggle');
    const text = document.getElementById('footer-seo-text');
    const label = toggle?.querySelector('.footer-seo__toggle-label');
    if (!toggle || !text || !label) return;

    /* CSS не знає auto-висоти наперед, тож max-height анімуємо inline-стилем,
       а висоту згорнутого стану (line-clamp: 2) міряємо самі. Перемір після
       fonts.ready обов'язковий: до підвантаження Geologica два рядки мають
       іншу висоту, і на цьому значенні згортання зупинилось би не там. */
    let collapsedHeight = text.getBoundingClientRect().height + 'px';
    const measure = () => {
      if (text.classList.contains('is-expanded')) return;
      collapsedHeight = text.getBoundingClientRect().height + 'px';
    };

    /* Кнопка не потрібна, якщо текст і так влазить у 2 рядки:
       scrollHeight > clientHeight означає, що clamp щось відрізає.
       Міряємо лише згорнутий стан; ResizeObserver ловить зміну ширини —
       на іншій ширині перенос інший, і 2 рядки можуть стати достатніми. */
    const sync = () => {
      if (text.classList.contains('is-expanded')) return;
      measure();
      toggle.hidden = text.scrollHeight <= text.clientHeight;
    };
    sync();
    new ResizeObserver(sync).observe(text);
    document.fonts?.ready.then(sync);

    toggle.addEventListener('click', () => {
      const expanded = text.classList.contains('is-expanded');
      if (expanded) {
        text.classList.remove('is-settled');
        text.style.maxHeight = text.scrollHeight + 'px';
        requestAnimationFrame(() => { text.style.maxHeight = collapsedHeight; });
        text.addEventListener('transitionend', () => {
          text.classList.remove('is-expanded');
          text.style.maxHeight = '';
        }, { once: true });
      } else {
        text.classList.add('is-expanded');
        text.style.maxHeight = collapsedHeight;
        requestAnimationFrame(() => { text.style.maxHeight = text.scrollHeight + 'px'; });
        /* maxHeight знімаємо після переходу, щоб блок далі жив на auto-висоті
           (інакше довгий текст обріжеться на заміряному значенні) */
        text.addEventListener('transitionend', () => {
          text.style.maxHeight = '';
          text.classList.add('is-settled');
        }, { once: true });
      }
      toggle.setAttribute('aria-expanded', String(!expanded));
      label.textContent = expanded ? toggle.dataset.labelMore : toggle.dataset.labelLess;
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
     main.css) лишається фолбеком там, де Lenis не піднявся взагалі
     (вендор не довантажився). -------------------- */
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

  /* ---- Dropdown (<details class="dropdown">): закриття кліком повз і по
     Escape. Саме відкриття, клавіатура й ARIA — нативні, JS тут лише
     доповнює те, чого <details> не вміє. --------------------------------- */
  (() => {
    const dropdowns = document.querySelectorAll('details.dropdown');
    if (!dropdowns.length) return;

    const closeAll = (except) => dropdowns.forEach((d) => {
      if (d !== except) d.open = false;
    });

    document.addEventListener('click', (e) => {
      const inside = e.target.closest('details.dropdown');
      closeAll(inside);
    });
    addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      const open = document.querySelector('details.dropdown[open]');
      if (!open) return;
      open.open = false;
      open.querySelector('summary')?.focus();
    });
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

  /* ---- Emboss — нерозривна плитка тиснення між сусідніми полями ------
     mask-position у .embossed рахується від власного боксу елемента, тож
     кожне тиснене поле починає плитку заново — і на межі двох сусідніх
     (CTA над футером) видно розрив. Прив'язуємо початок плитки до
     документа: --emboss-y = відступ поля від верху сторінки за модулем
     висоти плитки, і патерн проходить крізь межу як одне полотно.
     Модуль — щоб зсув лишався малим числом і не залежав від довжини
     сторінки. Висота плитки мусить збігатися з mask-size у styles.css. */
  (() => {
    const blocks = document.querySelectorAll('.embossed');
    if (!blocks.length) return;

    const TILE = 17.875 * parseFloat(getComputedStyle(document.documentElement).fontSize);

    const sync = () => {
      const top = window.scrollY || document.documentElement.scrollTop;
      blocks.forEach((el) => {
        const y = el.getBoundingClientRect().top + top;
        el.style.setProperty('--emboss-y', (y % TILE).toFixed(2) + 'px');
      });
    };

    sync();
    window.addEventListener('resize', sync);
    document.fonts?.ready.then(sync);
    // висота сусідів змінюється (розгорнутий SEO-текст, підвантажені кадри) —
    // тоді зсув треба перерахувати, інакше плитка знову розходиться
    new ResizeObserver(sync).observe(document.body);
  })();

})();
