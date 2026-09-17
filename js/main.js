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

  /* ---- Reveal "lines" — авто-врап тексту в .char по літерах ----------- */
  (() => {
    /* <br> лишається в розмітці як реальний розрив. Кожна літера — інлайновий
       .char (не inline-block: так браузер шейпить текст крізь спани і кернінг
       пар «Ту»/«Гл» не ламається). Пробіли лишаються голими текстовими
       вузлами, тому перенос рядків і word-spacing працюють як у звичайному
       тексті. Ефект жалюзі — маска в main.css, стагер по --char-i. */
    document.querySelectorAll('[data-reveal="lines"]').forEach((el) => {
      if (el.querySelector('.char')) return;
      const html = [];
      let i = 0;
      const chars = (text) => [...text].map((c) => `<span class="char" style="--char-i: ${i++}">${c}</span>`).join('');
      el.childNodes.forEach((node) => {
        if (node.nodeName === 'BR') { html.push('<br>'); return; }
        /* Інлайн-теги (<em> для акцентного слова) відтворюються навколо своїх
           літер: innerHTML нижче переписує вміст цілком, і без цього тег
           зникав би разом із акцентом. Вкладеність глибше одного рівня
           схлопується — заголовку вистачає. */
        const tag = node.nodeType === 1 ? node.tagName.toLowerCase() : '';
        const attrs = tag ? [...node.attributes].map((a) => ` ${a.name}="${a.value}"`).join('') : '';
        /* Розділювач — будь-який пробіл, КРІМ нерозривного: ним у розмітці
           привʼязане тире до попереднього слова (щоб не починало рядок), і
           звичайний \s з'їв би цей звʼязок, замінивши його на join(' '). */
        const words = (node.textContent || '').split(/[^\S\u00a0]+/).filter(Boolean).map(chars).join(' ');
        html.push(tag ? `<${tag}${attrs}>${words}</${tag}>` : words);
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
              el.textContent = orig.replace(digits, Math.round(target * eased).toLocaleString(document.documentElement.lang || undefined));
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

    // Tabs (нижче) перезапускає reveal своєї панелі при кожному відкритті —
    // той самий observer, щоб не плодити другий IntersectionObserver
    window._functions.rearmReveal = (root) => {
      root.querySelectorAll('[data-reveal]').forEach((el) => {
        el.classList.remove('is-visible');
        io.observe(el);
      });
    };
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

      /* data-anim="blinds" — стос кадрів, де кожен наступний відкривається
         жалюзі: одна секція на всю висоту, тож це не N планок, а одна
         горизонтальна щілина, що росте зверху вниз. Робимо clip-path'ом по
         inset: анімується композитором, layout не чіпає (див. CLAUDE.md).
         Той самий відрізок скролу, що й в одометра (центр першого кроку →
         центр останнього), — кадр міняється рівно тоді, коли клацає цифра. */
      document.querySelectorAll('[data-anim="blinds"]').forEach((el) => {
        const items = document.querySelector(el.dataset.blindsFor)?.children;
        const shots = el.children;
        if (!items || items.length < 2 || shots.length < 2) return;

        /* прогрес ділиться на (N-1) переходів; кожен наступний кадр
           відкривається на своєму відрізку, решту часу — повністю
           закритий (100%) або повністю відкритий (0%) */
        const steps = shots.length - 1;
        gsap.timeline({
          scrollTrigger: {
            trigger: items[0], start: 'center center',
            endTrigger: items[items.length - 1], end: 'center center',
            scrub: true,
          },
        }).fromTo([...shots].slice(1),
          { clipPath: 'inset(100% 0 0 0)' },
          {
            clipPath: 'inset(0% 0 0 0)',
            ease: 'none',
            stagger: { each: 1 / steps },
            duration: 1 / steps,
          }, 0);
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

    /* data-anim="pinstack" — секція пінується на N екранів. Два шари в
       різних ритмах: кадри (фон і той самий кадр у картці) відкриваються
       шторкою зі скролом, а тексти міняються стрибком на середині кроку —
       зсувом рядків зі стагером. Тому scrub веде лише кадри, а текст —
       звичайний твін фіксованої тривалості: на проміжній позиції скролу
       він інакше завмирав би напівпрозорим.
       Поза matchMedia вище навмисно: той гейт — лише десктоп, а тут ефект
       мусить працювати на всіх ширинах. */
    document.querySelectorAll('[data-anim="pinstack"]').forEach((sec) => {
      const bgs = sec.querySelectorAll('.pinstack__bg');
      const shots = sec.querySelectorAll('.pinstack__shot');
      const panels = sec.querySelectorAll('.pinstack__panel');
      if (panels.length < 2) return;
      // висоту секції (кількість екранів на прокрут) рахує CSS із цього числа
      sec.style.setProperty('--steps', panels.length);

      /* Стагер іде по рядках, а не по словах, тож рядки треба знайти вже
         після верстки: де саме ляже перенос, залежить від ширини картки
         й шрифту, і з розмітки це не видно. Ділимо на слова, читаємо
         offsetTop кожного і групуємо в один .line-mask на рядок.
         Текст тримаємо в data-text: після першого проходу в DOM уже
         обгортки, і textContent другого разу дав би склеєні слова. */
      const textOf = (el) => (el.dataset.text ??= el.textContent.trim());
      const splitLines = (el) => {
        /* Проміри — окремим класом (.line-probe, інлайновий), а не
           .line-mask: той у CSS block, і кожне слово стало б своїм рядком,
           тобто вимірювали б не той перенос, що буде насправді. */
        el.innerHTML = textOf(el).split(/[^\S\u00a0]+/)   // нерозривний — не розділювач (див. reveal="lines")
          .map((w) => `<span class="line-probe">${w}</span>`).join(' ');
        const words = [...el.children];
        /* Групуємо по offsetTop: слова одного рядка мають однаковий верх.
           Порівняння точне — усі слова рядка стоять на спільному baseline,
           а дробові значення дає лише масштаб сторінки, спільний для всіх. */
        const rows = [];
        words.forEach((w) => {
          const top = w.offsetTop;
          const row = rows[rows.length - 1];
          if (row && row.top === top) row.words.push(w);
          else rows.push({ top, words: [w] });
        });
        /* Перезбираємо: два спани на рядок. Зовнішній .line-clip обрізає
           (overflow), внутрішній .line-mask їздить — рухати й обрізати
           той самий елемент не можна, він обрізав би сам себе.
           Завдяки цьому рядок ховається за власною межею й не наїжджає
           на сусідній, поки хвиля йде.
           Пробіли лишаються всередині рядка, а МІЖ рядками їх немає —
           рядки тут блоки, і пробіл між ними став би зайвим порожнім
           рядковим боксом, який розсунув би текст по вертикалі. */
        el.innerHTML = rows
          .map((r) => `<span class="line-clip"><span class="line-mask">${
            r.words.map((w) => w.textContent).join(' ')}</span></span>`)
          .join('');
        /* Анімуємо внутрішні спани, не обгортки: рухається .line-mask */
        return [...el.querySelectorAll('.line-mask')];
      };
      const parts = [...panels].map((p) =>
        [p.querySelector('.pinstack__title'), p.querySelector('.pinstack__desc')]);
      /* Ділити треба до того, як щось приховано: display:contents лишає
         панелі в потоці, тож offsetTop чесний для всіх трьох.
         Одразу після поділу ховаємо неактивні — у CSS цього нема
         навмисно (див. .pinstack__panel:not(.is-active)): якби opacity
         стояла там, зміна класу гасила б панель миттєво і вихідна
         анімація рядків не встигала б відпрацювати. */
      let lines = parts.map((els) => els.flatMap(splitLines));
      lines.forEach((ws, k) => { if (k) gsap.set(ws, { opacity: 0 }); });
      sec.classList.add('is-ready');   // знімає fallback-visibility з CSS

      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: sec,
          start: 'top top',
          end: 'bottom bottom',
          pin: '.pinstack__stage',
          pinSpacing: false,   // висоту вже дає сама секція (calc зі --steps)
          scrub: true,
        },
      });

      /* Фон — жалюзі: маска зі SLATS однакових смуг, у кожній видима частка
         росте від 0 до 1. Пишемо градієнт у CSS-змінну щоразу, бо CSS не
         вміє нагенерувати N колірних стопів сам.
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

      /* Крок — рівно 1 умовна секунда таймлайна, щоб кожна панель займала
         однакову частку скролу (на цьому тримається поріг зміни тексту).
         Фон і кадр у картці йдуть в одному кроці, але різними прийомами:
         фон — жалюзі в багато смуг (проксі-обʼєкт, бо градієнт треба
         перезбирати щокадру), картка — одна шторка знизу вгору. На
         повний екран смуги читаються, у кадрі 3:2 вони були б дрібними. */
      panels.forEach((panel, i) => {
        if (!i) return;
        const m = { open: 0 };
        tl.to(m, {
          open: 1, ease: 'none', duration: 0.75,
          onUpdate: () => setMask(bgs[i], m.open),
        }, i - 1);
        tl.fromTo(shots[i],
          { clipPath: 'inset(100% 0 0 0)' },
          { clipPath: 'inset(0% 0 0 0)', ease: 'none', duration: 0.75 }, i - 1);
      });

      /* Повільний наїзд фону на весь пін — один твін на всі панелі, а не
         по одному на крок: інакше на межі кроків масштаб стрибав би назад
         на 1. Ціль — спільний .pinstack__bgs, а не кожен .pinstack__bg: у
         момент переходу два кадри видно одночасно (один із-під шторки
         іншого), і на різних масштабах шов між ними був би помітний. */
      tl.fromTo(sec.querySelector('.pinstack__bgs'),
        { scale: 1 }, { scale: 1.15, ease: 'none', duration: panels.length - 1 }, 0);

      /* Той самий наїзд у картці, але дрібніший: кадр там у рази менший
         за екран, і 15% на ньому читались би як ривок, а не як повільний
         рух. Масштабуємо самі кадри, а не .pinstack__shots: та рамка —
         видима межа картки, і разом із нею кадр вилазив би за край.
         Спільним твіном на всі кадри — під час шторки видно два одразу,
         і на різних масштабах шов між ними був би помітний. */
      tl.fromTo(shots,
        { scale: 1 }, { scale: 1.2, ease: 'none', duration: panels.length - 1 }, 0);

      let shown = 0;
      const swapTo = (n) => {
        if (n === shown) return;
        const from = lines[shown], to = lines[n];
        shown = n;
        /* Клас ставимо одразу всім, а не в onComplete: при швидкому
           скролі туди-назад killTweensOf обриває твін, і onComplete не
           настав би — панель лишилась би .is-active назавжди. */
        panels.forEach((p, k) => p.classList.toggle('is-active', k === n));
        lines.forEach((ws) => gsap.killTweensOf(ws));
        /* Перестрибнуту панель (швидкий скрол 1→3) гасимо без анімації —
           інакше на ній лишиться інлайнова opacity від обірваного твіна */
        lines.forEach((ws, k) => {
          if (k !== n && ws !== from) gsap.set(ws, { opacity: 0, yPercent: 0 });
        });
        /* Рядки йдуть зі стагером в обидва боки: старі вибувають угору,
           нові набігають знизу. Вихід і вхід дзеркальні — та сама
           тривалість, той самий крок стагера й та сама дорога (60%):
           інакше рядки зникали б помітно швидше, ніж зʼявляються.
           Затримка між рядками (0.09) читається як хвиля згори вниз;
           на 0.04 три рядки рушали майже разом.
           Ease парний: power2.in на вихід і power2.out на вхід — рядок
           розганяється, йдучи, і гальмує, приходячи. power3 на такій
           короткій дорозі давав ривок на старті.
           Дорога — 110% власної висоти, а не рівно 100%: у заголовка
           вікно .line-clip відсунуте на 0.08em під виносні, тож на
           100% низ рядка спинявся б рівно на цій щілині й визирав з
           неї. 110% перекриває запас і в заголовка, і в тексту. */
        gsap.to(from, {
          opacity: 0, yPercent: -110, duration: 0.5, ease: 'power2.in',
          stagger: 0.09,
        });
        gsap.fromTo(to, { opacity: 0, yPercent: 110 }, {
          opacity: 1, yPercent: 0, duration: 0.5, ease: 'power2.out',
          stagger: 0.09, delay: 0.34,
        });
      };
      /* Твіни останнього кроку закінчуються трохи раніше за рівну довжину —
         порожній твін у кінці добиває таймлайн до цілого числа кроків.
         totalDuration() тут не годиться: він розтягнув би самі твіни, а
         нам треба рівні кроки — на них тримається поріг зміни панелі. */
      tl.to({}, { duration: 0 }, panels.length - 1);
      /* Індекс беремо з прогресу таймлайна, а не .set() на кроці: set при
         скролі вгору лишає останнє значення, а прогрес однаково правильний
         в обидва боки. Поріг — рівно половина кроку (+0.5): текст міняється
         на середині шторки, коли новий кадр відкрито наполовину. */
      tl.eventCallback('onUpdate', () => {
        swapTo(Math.min(panels.length - 1,
          Math.floor(tl.progress() * (panels.length - 1) + 0.5)));
      });

      /* Перенос рядків залежить від ширини — на ресайзі поділ треба
         зробити наново. Ділити на льоту (у листенері) не можна: splitLines
         переписує innerHTML і губить інлайнові стилі від GSAP, тож після
         поділу вручну відновлюємо стан — видима панель у нулі, решта
         прихована. Той самий debounce, що й у ScrollTrigger.refresh(). */
      let t;
      window.addEventListener('resize', () => {
        clearTimeout(t);
        t = setTimeout(() => {
          lines.forEach((ws) => gsap.killTweensOf(ws));
          lines = parts.map((els) => els.flatMap(splitLines));
          lines.forEach((ws, k) => gsap.set(ws, { opacity: k === shown ? 1 : 0, yPercent: 0 }));
        }, 200);
      });
    });

    /* Рефреш не з нульової позиції: браузер відновлює скрол асинхронно —
       після defer-скриптів, шрифтів і завантаження зображень, коли висота
       сторінки ще мінялась (і при відкритті з відновленою/якірною позицією
       скролу). Тригери, пораховані до цього, лишаються зміщеними — тож
       перераховуємо їх, коли сторінка вже має фінальну висоту. */
    document.fonts?.ready.then(() => ScrollTrigger.refresh());
    window.addEventListener('load', () => ScrollTrigger.refresh());

    /* Будь-яка зміна висоти документа (акордеон, таби, AJAX-контент
       розкладу тощо) зсуває пін-треки нижче на сторінці — один спільний
       ResizeObserver замість ручного ScrollTrigger.refresh() у кожному
       місці, де міняється висота. Debounce той самий, що й раніше в
       Accordion/Tabs. */
    let rt;
    new ResizeObserver(() => {
      clearTimeout(rt);
      rt = setTimeout(() => ScrollTrigger.refresh(), 200);
    }).observe(document.body);
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

    // [data-tab-panel][hidden] (Tabs нижче): ширина 0 на сховану панель дала
    // б Swiper порахувати нульові слайди — ті ініціалізує сам Tabs.fadeIn
    // при першому показі, через той самий initSwiper (без другого джерела
    // логіки для однієї й тієї ж лінивої ініціалізації).
    document.querySelectorAll('.swiper').forEach((el) => {
      if (el.closest('[data-tab-panel][hidden]')) return;
      initSwiper(el);
    });
    window._functions.initSwiper = initSwiper;
  })();

  /* ---- GLightbox: фулскрін-перегляд галерей (vendor підключає лише
     сторінка, що виставила $vendor_lightbox перед include footer.php).
     Esc, клавіатуру, свайп, лічильник і фокус вендор бере на себе. ----- */
  (() => {
    if (typeof GLightbox === 'undefined') return;
    /* Стрілка в спрайті одна (icon-arrow-right) — «назад» це вона ж,
       відзеркалена класом .glightbox-prev-icon у styles.css. */
    const glIcon = (id) => `<svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#${id}"></use></svg>`;
    const OPTIONS = {
      loop: true,
      touchNavigation: true,
      svg: {
        close: glIcon('icon-close'),
        next: `<span class="glightbox-next-icon">${glIcon('icon-arrow-right')}</span>`,
        prev: `<span class="glightbox-prev-icon">${glIcon('icon-arrow-right')}</span>`,
      },
    };
    /* Lenis рухає сторінку через window.scrollTo — клас glightbox-open з
       overflow:hidden на це не впливає, і фон їхав би під відкритим
       лайтбоксом. Глушимо явно, як це роблять модалки через body overflow. */
    const bindScrollLock = (lb) => {
      lb.on('open', () => lenisInstance?.stop());
      lb.on('close', () => lenisInstance?.start());
      return lb;
    };

    const lightbox = bindScrollLock(GLightbox({ ...OPTIONS, selector: '[data-glightbox]' }));

    /* Loop-дублікати слайдера в галерею не входять (див. partials/gallery.php),
       тож клік по них вендор не ловить — переадресовуємо на оригінал.
       Саме клік, а не lightbox.openAt(index): openAt відкриває без елемента,
       тож вендор не бачить його data-gallery і бере весь список [data-glightbox]
       сторінки (зали + сертифікати + відеовідгук) одним набором. */
    document.addEventListener('click', (e) => {
      const dup = e.target.closest('.gallery__zoom[data-gl-index]:not([data-glightbox])');
      if (!dup) return;
      e.preventDefault();
      dup.closest('.swiper-wrapper')
        ?.querySelector(`.gallery__zoom[data-glightbox][data-gl-index="${dup.dataset.glIndex}"]`)
        ?.click();
    });

    /* Для галерей, які не тримаються на посиланнях у розмітці (стос карток:
       картку тягнуть вказівником, тож <a> там був би пасткою для жесту).
       Набір кадрів приходить масивом — інстанс живе рівно одне відкриття. */
    window._functions.openLightbox = (items, index = 0) => {
      const lb = bindScrollLock(GLightbox({
        ...OPTIONS,
        elements: items.map(({ href, alt }) => ({ href, type: 'image', alt })),
        startAt: index,
      }));
      lb.on('close', () => setTimeout(() => lb.destroy(), 0));
      lb.open();
    };
  })();

  /* ---- Card stack: фото стосом, передню картку тягнемо вказівником.
     Відпущена за порогом картка йде В КІНЕЦЬ стосу незалежно від напрямку
     жесту — назад повертає тільки кнопка «Попереднє».
     Swiper тут навмисно не використаний: секція мусить працювати й на
     сторінці, яка не виставила $vendor_swiper.
     Порядок тримає масив індексів + CSS-змінна --i на кожній картці.
     Переставляти вузли в DOM не можна: це скидало б transition карток,
     що саме їдуть, губило б фокус і змушувало браузер перемальовувати
     <img>. --------------------------------------------------------- */
  function initCardStack(root) {
    const frame = root.querySelector('.cardstack__frame');
    const section = root.closest('.cardstack') ?? root;
    const cards = [...root.querySelectorAll('[data-card]')];
    // усі, не querySelector: розмітка дублює лічильник (десктоп/мобільний)
    const out = [...root.querySelectorAll('[data-stack-current]')];
    if (!frame || cards.length < 2) return;

    // order[0] — картка попереду; значення — індекс у cards
    let order = cards.map((_, i) => i);
    let busy = false;   // поки картка відлітає, нові жести ігноруємо

    // скільки карток видно, рахуючи передню — те саме число, що --stack-depth
    // у CSS: рамка резервує під «хвости» рівно (depth - 1) кроків
    const depth = parseInt(getComputedStyle(frame).getPropertyValue('--stack-depth'), 10) || 4;

    const render = () => {
      order.forEach((cardIdx, pos) => {
        const card = cards[cardIdx];
        // картку в польоті не чіпаємо: її позицію тримає .is-flying, поки
        // вона не долетить (інлайновий --i переважив би клас)
        if (card.classList.contains('is-flying')) return;
        // Приховані стоять на позиції depth — на крок ГЛИБШЕ за останню
        // видиму, а не на її місці: інакше вихід із глибини не мав би куди
        // рухатись, і нова картка проявлялась би самою прозорістю, без
        // зсуву й скейлу, якими їде решта стосу.
        card.style.setProperty('--i', Math.min(pos, depth));
        card.classList.toggle('is-deep', pos > depth - 1);
        card.classList.toggle('cardstack__card--front', pos === 0);
        // фокус лише на передній: табом не треба проходити крізь увесь стос
        card.tabIndex = pos === 0 ? 0 : -1;
      });
      // data-photo, а не order[0]: коли карток у DOM більше, ніж унікальних
      // фото (мало кадрів — PHP добирає стос дублями), номер картки й номер
      // фото розходяться, а лічильник мусить показувати саме друге
      if (out.length) {
        const front = cards[order[0]];
        const photo = parseInt(front.dataset.photo ?? order[0], 10);
        const text = String(photo + 1).padStart(2, '0');
        out.forEach((el) => { el.textContent = text; });
      }
    };

    // повертає картку до чистої стосової форми, порахованої в CSS
    const clearDrag = (card) => {
      card.style.removeProperty('--dx');
      card.style.removeProperty('--dy');
    };

    /* Уперед: картка падає ВНИЗ за край кадру, і стос їде вперед ОДРАЗУ,
       разом із початком падіння, — не чекаючи, поки вона долетить. Тобто
       наступна картка виїжджає на передній план своїм же transition (у неї
       міняється --i, а з ним translate і scale), а лічильник перемикається
       тієї ж миті.
       Щоб та, що падає, не стрибнула в кінець стосу просто в польоті,
       .is-flying тримає її на позиції 0 (CSS), поки не долетить.
       transitionend, а не setTimeout — тривалість живе в CSS. */
    const next = () => {
      if (busy) return;
      busy = true;
      const card = cards[order[0]];
      card.classList.remove('is-dragging');
      card.classList.add('is-leaving', 'is-flying');
      // --dx лишався б від жесту й тягнув би картку вбік — падіння рівно вниз
      card.style.setProperty('--dx', '0px');
      card.style.setProperty('--dy', '120%');
      // стос і лічильник рушають зараз, а не в transitionend
      order.push(order.shift());
      render();
      card.addEventListener('transitionend', function done(e) {
        // opacity їде тим самим переходом — без перевірки спрацювало б двічі
        if (e.target !== card || e.propertyName !== 'transform') return;
        card.removeEventListener('transitionend', done);
        /* Повернення в стос НЕ анімуємо: картка вже впала й невидима, а
           будь-який перехід тут означав би, що вона їде з-під низу кадру
           назад на своє місце — тобто знову з'являється в кадрі.
           Порядок значущий: .is-dragging глушить transition, далі знімаємо
           зсув і .is-flying (тепер render() дасть їй справжній --i), і аж
           після подвійного rAF — коли браузер намалював її на новому
           місці — перехід вертається. */
        card.classList.add('is-dragging');
        card.classList.remove('is-leaving', 'is-flying');
        clearDrag(card);
        render();
        requestAnimationFrame(() => requestAnimationFrame(() => {
          card.classList.remove('is-dragging');
          busy = false;
        }));
      });
    };

    /* Назад: остання картка вертається ЗГОРИ — рух, зворотний до падіння.
       Спершу ставимо її за кадром із заглушеним переходом, потім
       відпускаємо на місце. Подвійний rAF — той самий прийом, що у fadeIn
       (Tabs нижче): з одинарним браузер склеїв би обидві зміни стилю в
       один recalculation і руху не було б. */
    const prev = () => {
      if (busy) return;
      busy = true;
      order.unshift(order.pop());
      const card = cards[order[0]];
      card.classList.add('is-dragging');
      card.style.setProperty('--dy', '-120%');
      render();
      requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('is-dragging');
        clearDrag(card);
        card.addEventListener('transitionend', function done(e) {
          if (e.target !== card || e.propertyName !== 'transform') return;
          card.removeEventListener('transitionend', done);
          busy = false;
        });
      }));
    };

    /* Жест. setPointerCapture обовʼязковий: палець може зійти з картки й
       навіть із вікна — без захоплення pointerup прилетів би не сюди, і
       картка застрягла б у зсунутому стані. */
    const THRESHOLD = 90;   // px; менший зсув — картка вертається на місце
    let startX = 0, startY = 0, dx = 0, dy = 0, dragCard = null;

    frame.addEventListener('pointerdown', (e) => {
      if (busy || e.button !== 0) return;
      const card = e.target.closest('.cardstack__card--front');
      if (!card) return;
      dragCard = card;
      startX = e.clientX;
      startY = e.clientY;
      dx = dy = 0;
      card.setPointerCapture(e.pointerId);
      card.classList.add('is-dragging');
    });

    frame.addEventListener('pointermove', (e) => {
      if (!dragCard) return;
      dx = e.clientX - startX;
      dy = e.clientY - startY;
      dragCard.style.setProperty('--dx', dx + 'px');
      dragCard.style.setProperty('--dy', dy + 'px');
      // setPointerCapture ловить рух і за межами кадру — без цієї перевірки
      // картку можна було б тягнути будь-як далеко, поки палець не підняли.
      // Межа — вся секція (не сама рамка): заголовок і контроли лишаються
      // «своєю» територією жесту, відпускає тільки вихід за секцію повністю.
      const r = section.getBoundingClientRect();
      if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) {
        endDrag();
      }
    });

    /* Кадри для лайтбокса — лише унікальні: PHP добирає стос дублями до
       глибини (див. partials/cardstack.php), а data-photo несе номер
       оригіналу, за яким дублікати й відсіюються. */
    const photos = [];
    cards.forEach((card) => {
      const idx = parseInt(card.dataset.photo ?? '0', 10);
      const img = card.querySelector('img');
      if (photos[idx] || !img) return;
      photos[idx] = { href: img.currentSrc || img.src, alt: img.alt };
    });

    const endDrag = () => {
      if (!dragCard) return;
      const card = dragCard;
      dragCard = null;
      card.classList.remove('is-dragging');
      // кидок у будь-який бік відправляє картку вниз
      if (Math.hypot(dx, dy) > THRESHOLD) { next(); return; }
      clearDrag(card);   // під поріг — пружина назад, веде CSS
      /* Той самий жест під порогом — це вже не кидок, а тап: відкриваємо
         кадр на весь екран. Тому лайтбокс висить тут, а не на click:
         click прилітає й після справжнього перетягування, і картка
         відкривалась би щоразу, коли її просто гортають. */
      if (Math.hypot(dx, dy) < 6) {
        window._functions.openLightbox?.(photos, parseInt(card.dataset.photo ?? '0', 10));
      }
    };
    frame.addEventListener('pointerup', endDrag);
    frame.addEventListener('pointercancel', endDrag);

    root.querySelector('[data-stack-next]')?.addEventListener('click', () => next());
    root.querySelector('[data-stack-prev]')?.addEventListener('click', prev);
    frame.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') next();
      else if (e.key === 'ArrowLeft') prev();
      else if (e.key === 'Enter' || e.key === ' ') {
        const front = cards[order[0]];
        window._functions.openLightbox?.(photos, parseInt(front.dataset.photo ?? '0', 10));
      } else return;
      e.preventDefault();
    });

    frame.classList.add('is-live');
    render();
  }

  document.querySelectorAll('[data-cardstack]').forEach(initCardStack);

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

  /* ---- Форми: маска телефону (IMask) + кастомний <select> (SlimSelect).
     Обидві функції в window._functions: поля живуть у модалках, які
     приходять через AJAX пізніше, тож модальний loader нижче кличе їх
     на вставлений фрагмент. Селектор по type="tel", не по класу —
     покриває будь-яку форму, наявну чи майбутню. ----------------------- */
  window._functions.applyPhoneMask = (root) => {
    if (typeof window.IMask === 'undefined') return;
    root.querySelectorAll('input[type="tel"]:not([data-imask-bound])').forEach((el) => {
      el.dataset.imaskBound = '';
      const mask = IMask(el, { mask: '+{380} 00 000 00 00' });
      // Неповний номер на blur скидаємо в порожнє поле: нативний required
      // тоді ловить його як звичайне незаповнене, без setCustomValidity.
      el.addEventListener('blur', () => {
        if (el.value && !mask.masked.isComplete) mask.value = '';
      });
    });
  };
  window._functions.applySlimSelect = (root) => {
    if (typeof window.SlimSelect === 'undefined') return;
    // showSearch: false — переліки короткі, поле пошуку в них лише шум.
    // Клас із <select> вендор копіює на свій .ss-main, тож .form__input
    // на селекті дає полю той самий вигляд, що й інпутам.
    root.querySelectorAll('select[data-slimselect]:not([data-ss-bound])').forEach((el) => {
      el.dataset.ssBound = '';
      new window.SlimSelect({ select: el, settings: { showSearch: false } });
    });
  };
  window._functions.applyPhoneMask(document);
  window._functions.applySlimSelect(document);

  /* ---- Modal overlay: shared AJAX loader for partials/modals/* -------- */
  (() => {
    const overlay = document.getElementById('modal-overlay');
    const content = document.getElementById('modal-overlay-content');
    const panel = document.getElementById('modal-overlay-panel');
    const loader = document.getElementById('modal-loader');
    if (!overlay || !content || !panel || !loader) return;

    let trigger = null;

    const open = () => {
      overlay.hidden = false;
      // подвійний rAF, не одинарний: перший показ оверлея (ще ніколи не був
      // видимим) браузер інколи склеює з hidden→false в один кадр без переходу
      requestAnimationFrame(() => {
        requestAnimationFrame(() => overlay.classList.add('is-open'));
      });
      document.body.style.overflow = 'hidden';
    };
    const close = () => {
      overlay.classList.remove('is-open');
      panel.classList.remove('is-ready');
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
      // спінер — окремий елемент поза .modal-overlay__panel: панель ховаємо
      // цілком, щоб її розмір не стрибав при заміні спінера на контент
      panel.classList.remove('is-ready');
      panel.hidden = true;
      loader.hidden = false;
      open();
      try {
        const res = await fetch(`partials/modals/loader.php?${qs}`);
        if (!res.ok) { close(); return; }
        content.innerHTML = await res.text();
        loader.hidden = true;
        panel.hidden = false;
        // panel щойно з'явився в DOM (був display:none) — is-open на overlay
        // вже стоїть, тож перехід без власного кадру «до» не запуститься;
        // is-ready — окремий контрол саме на панелі, подвійний rAF як в open()
        requestAnimationFrame(() => {
          requestAnimationFrame(() => panel.classList.add('is-ready'));
        });
        overlay.querySelector('.modal-overlay__close')?.focus();
        // поля модалки щойно в DOM — стартовий прохід по document їх не бачив
        window._functions.applyPhoneMask(content);
        window._functions.applySlimSelect(content);
      } catch { close(); }
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

  /* ---- Cookie-інформер: показуємо, поки його не закрили. Просто
     повідомлення, не згода — вибору «прийняти/відхилити» немає, бо немає
     й скриптів, які треба було б під нього гейтити. Прапорець у
     localStorage, не в самій cookie: бек про нього нічого не питає. */
  (() => {
    const el = document.getElementById('cookie');
    if (!el) return;
    const KEY = 'cookie-notice';
    let seen = null;
    // приватний режим Safari кидає на самому доступі до localStorage
    try { seen = localStorage.getItem(KEY); } catch { /* показуємо інформер */ }
    if (seen) return;

    el.hidden = false;
    requestAnimationFrame(() => el.classList.add('is-visible'));

    el.querySelector('[data-cookie-close]')?.addEventListener('click', () => {
      try { localStorage.setItem(KEY, '1'); } catch { /* закриття не переживе перезавантаження */ }
      el.classList.remove('is-visible');
      el.addEventListener('transitionend', () => { el.hidden = true; }, { once: true });
    });
  })();

  /* ---- Промо-попап (маркетинг): автопоказ через таймер, раз на сесію.
     sessionStorage, не localStorage — на відміну від cookie-інформера вище,
     повторний візит наступного дня має показати його знову. Незалежний від
     info-corner/topbar нижче — кожен своїм таймером (рішення проєкту). */
  (() => {
    if (!document.body.dataset.home) return;   // маркетинг — лише на головній
    const KEY = 'promo-popup-seen';
    let seen = null;
    try { seen = sessionStorage.getItem(KEY); } catch { /* показуємо попап */ }
    if (seen) return;

    setTimeout(() => {
      const overlay = document.getElementById('modal-overlay');
      if (overlay && overlay.hidden === false) return; // вже відкрита інша модалка
      try { sessionStorage.setItem(KEY, '1'); } catch { /* закриття не переживе перезавантаження */ }
      window._functions.loadModal?.('promo');
    }, 4000);
  })();

  /* ---- Info corner: маркетинговий попап у кутку екрана. Той самий
     протокол, що й cookie-інформер вище, окремий sessionStorage-ключ. */
  (() => {
    const el = document.getElementById('info-corner');
    if (!el) return;
    const KEY = 'info-corner-seen';
    let seen = null;
    try { seen = sessionStorage.getItem(KEY); } catch { /* показуємо попап */ }
    if (seen) return;

    setTimeout(() => {
      el.hidden = false;
      requestAnimationFrame(() => el.classList.add('is-visible'));
    }, 2000);

    el.querySelector('[data-info-corner-close]')?.addEventListener('click', () => {
      try { sessionStorage.setItem(KEY, '1'); } catch { /* закриття не переживе перезавантаження */ }
      el.classList.remove('is-visible');
      el.addEventListener('transitionend', () => { el.hidden = true; }, { once: true });
    });
  })();

  /* ---- «Надіслати код ще раз» у модалці auth: код шлеться на місці, без
     переходу на інший екран — кнопка просто гасне на хвилину, щоб клієнт
     не замовив десять SMS підряд. Делеговано на document: модалка
     приходить AJAX-ом, вішати слухач при старті нема на що.
     Реальний POST /auth/phone_reset_password/ додасть бек (data-auth). */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-resend]');
    if (!btn || btn.disabled) return;
    const label = btn.textContent.trim();
    let left = 60;
    const tick = () => {
      btn.textContent = `Надіслати код ще раз (${String(left).padStart(2, '0')}\u00a0с)`;
    };
    btn.disabled = true;
    tick();
    const id = setInterval(() => {
      // модалку могли закрити раніше — кнопки в DOM уже немає, рахувати нема для кого
      if (!btn.isConnected) { clearInterval(id); return; }
      if (--left > 0) { tick(); return; }
      clearInterval(id);
      btn.disabled = false;
      btn.textContent = label;
    }, 1000);
  });

  /* ---- Сабміт будь-якої .form → модалка подяки замість реального POST
     (беку під форми поки немає). Делеговано на document: форми живуть у
     модалках і в DOM на момент підписки ще не існують.

     [data-remote] — виняток: форми кабінету й входу підуть реальним
     запитом на свої ендпоінти (docs/ACCOUNT-WP.md §12). Без цієї перевірки
     вхід у кабінет мовчки перетворювався б на «дякуємо за заявку», ще й із
     form.reset() нижче, який стирає введене. */
  document.addEventListener('submit', (e) => {
    const form = e.target.closest('.form');
    if (!form || form.hasAttribute('data-remote') || e.defaultPrevented) return;
    e.preventDefault();
    form.reset();
    window._functions.loadModal('thanks');
  });


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
      window._functions.rearmReveal?.(panel);
      // Swiper пропущений глобальною ініціалізацією, поки панель була
      // [hidden] (нульова ширина) — тепер вона видима, можна порахувати слайди
      panel.querySelectorAll('.swiper:not(.swiper-initialized)').forEach((el) => window._functions.initSwiper?.(el));
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
        b.classList.toggle('is-current', on);
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

    // [data-tab-goto="slug"] — посилання-якір, що одразу перемикає на
    // потрібну тему (напр. лінк із іншої секції на конкретний тариф)
    document.querySelectorAll('[data-tab-goto]').forEach((link) => {
      const slug = link.dataset.tabGoto;
      if (!panels.some((p) => p.dataset.tabPanel === slug)) return;
      link.addEventListener('click', () => select(slug));
    });

    // is-hidden одразу, не лише hidden: без нього перший показ панелі (яка
    // ще ніколи не була "leaving") не має з чого анімувати вхід — fadeIn
    // знімає клас, якого й так нема, і панель стрибає в кінцевий стан різко
    panels.forEach((p) => { if (p !== current) { p.hidden = true; p.classList.add('is-hidden'); setDisabled(p, true); } });
    if (current) current.classList.remove('is-hidden');
  }

  document.querySelectorAll('[data-tabs]').forEach(initTabs);

  /* ---- Pattern — нерозривна плитка патерну між сусідніми полями ------
     mask-position у .patterned рахується від власного боксу елемента, тож
     кожне поле починає плитку заново — і на межі двох сусідніх
     (CTA над футером) видно розрив. Прив'язуємо початок плитки до
     документа: --pattern-y = відступ поля від верху сторінки за модулем
     висоти плитки, і патерн проходить крізь межу як одне полотно.
     Модуль — щоб зсув лишався малим числом і не залежав від довжини
     сторінки. Висота плитки мусить збігатися з mask-size у styles.css. */
  (() => {
    const blocks = document.querySelectorAll('.patterned');
    if (!blocks.length) return;

    const TILE = 17.875 * parseFloat(getComputedStyle(document.documentElement).fontSize);

    const sync = () => {
      const top = window.scrollY || document.documentElement.scrollTop;
      blocks.forEach((el) => {
        const y = el.getBoundingClientRect().top + top;
        el.style.setProperty('--pattern-y', (y % TILE).toFixed(2) + 'px');
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
