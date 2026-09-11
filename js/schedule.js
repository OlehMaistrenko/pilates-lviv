/* ============================================================
   Розклад: AJAX-підміна поверх звичайних GET-переходів
   ============================================================ */
(() => {
  'use strict';

  /* Усе, що є в #schedule (перемикач день/тиждень/місяць, стрілки періоду,
     форма фільтрів), — звичайні <a href> і GET-форма: без JS чи коли fetch
     впав, посилання/сабміт просто перезавантажують сторінку з новим
     query string (partials/schedule.php сам рендерить той самий вміст).
     Тут ловимо ті самі кліки/сабміт, тягнемо фрагмент через fetch і
     підміняємо DOM на місці — no-JS шлях лишається робочим фолбеком,
     не видаленим кодом.
     Ендпоінт — та сама сторінка (?view=&date=&…), з заголовком
     X-Requested-With: partials/schedule.php бачить його й друкує лише
     вміст .container (див. коментар там). Контракт — HTML-фрагмент, не
     JSON: розмітку слота/сітки місяця й так малює PHP
     (partials/schedule-slot.php), другий рендer тієї ж розмітки на JS
     дублював би логіку й розходився б із нею. */
  const root = document.getElementById('schedule');
  if (!root) return;

  const container = root.querySelector('.container');
  if (!container) return;

  // reveal-спостерігач (js/main.js) сканує DOM один раз при завантаженні
  // й нових елементів після підміни innerHTML не побачить — [data-reveal]
  // лишився б назавжди opacity:0 (дефолт у css/main.css). Секція вже на
  // екрані в момент кліку по фільтру (інакше по ньому нема як клікнути),
  // тож фрагмент одразу показуємо видимим, а не чекаємо повторний скрол.
  const revealNow = (scope) => {
    scope.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
  };

  // Оптимістичне оновлення бару (підпис періоду + href стрілок prev/next):
  // рахуємо на клієнті синхронно в момент кліку, до відповіді fetch.
  // Навіщо стрілки, а не лише підпис: href стрілки "next" у розмітці —
  // "поточна дата + крок", тобто сама вона на клієнті не знає наступного
  // кроку, доки сервер не перемалює бар. Якщо не оновити її тут, другий
  // швидкий клік по тій самій фізичній стрілці (DOM ще не встиг
  // замінитись відповіддю на перший) прочитає той самий href і обидва
  // кліки полетять на одну дату. Формат підпису — Intl.DateTimeFormat
  // (той самий ICU, що серверний IntlDateFormatter у partials/schedule.php);
  // рік дописуємо окремим текстом, бо 'year: numeric' в опціях Intl
  // додає "р." (уk-UA locale suffix), якого нема в PHP-патерні "d MMMM y".
  const captionFmt = {
    dayMonth:   new Intl.DateTimeFormat('uk-UA', { day: 'numeric', month: 'long' }),
    dayMonthShort: new Intl.DateTimeFormat('uk-UA', { day: 'numeric', month: 'short' }),
    month: new Intl.DateTimeFormat('uk-UA', { month: 'long' }),
  };
  const STEP_DAYS = { day: 1, week: 7 }; // 'month' рахуємо окремо — змінна довжина

  // Y-m-d з локальних компонентів, не toISOString(): той конвертує в UTC,
  // і в часовому поясі попереду UTC (Europe/Kyiv, +2/+3) північ зсувається
  // на попередню добу — крок "+1 день" повертав би ту саму дату, що й була.
  const toDateParam = (date) => {
    const pad = (n) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
  };

  const withDate = (url, date) => {
    const u = new URL(url, location.href);
    u.searchParams.set('date', toDateParam(date));
    return u.toString();
  };

  const updateBarNow = (url) => {
    const params = new URL(url, location.href).searchParams;
    const view = params.get('view') || 'week';
    const dateStr = params.get('date');
    if (!dateStr || !/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return; // "сьогодні"/нетипове — чекаємо сервер
    const date = new Date(`${dateStr}T00:00:00`);
    const year = date.getFullYear();

    // Межі видимого проміжку — ті самі, що server-side ($sch_from/$sch_to):
    // потрібні і для підпису тижня, і щоб знати, чи видно сьогодні.
    // monday/sunday — з понеділка, як 'monday this week' у PHP.
    const monday = (d) => {
      const r = new Date(d);
      r.setDate(r.getDate() - ((r.getDay() + 6) % 7));
      return r;
    };
    let from = date, to = date;
    if (view === 'week') {
      from = monday(date);
      to = new Date(from);
      to.setDate(to.getDate() + 6);
    } else if (view === 'month') {
      // сітка місяця добивається до повних тижнів, як і на сервері
      from = monday(new Date(year, date.getMonth(), 1));
      to = monday(new Date(year, date.getMonth() + 1, 0));
      to.setDate(to.getDate() + 6);
    }

    const caption = root.querySelector('.schedule__caption');
    if (caption) {
      if (view === 'day') caption.textContent = `${captionFmt.dayMonth.format(date)} ${year}`;
      else if (view === 'month') caption.textContent = `${captionFmt.month.format(date)} ${year}`;
      else caption.textContent = `${captionFmt.dayMonthShort.format(from)} — ${captionFmt.dayMonthShort.format(to)} ${to.getFullYear()}`;
    }

    const prev = root.querySelector('.schedule__arrow--prev');
    const next = root.querySelector('.schedule__nav a[rel="next"]');
    if (prev || next) {
      const step = (d, dir) => {
        const r = new Date(d);
        if (view === 'month') r.setMonth(r.getMonth() + dir);
        else r.setDate(r.getDate() + dir * STEP_DAYS[view]);
        return r;
      };
      if (prev) prev.href = withDate(url, step(date, -1));
      if (next) next.href = withDate(url, step(date, 1));
    }

    // «Сьогодні» лежить поза .schedule__nav, тож її поява/зникнення
    // не зсуває стрілки — але ховати її треба тут-таки, разом із баром.
    // Умова та сама, що в partials/schedule.php: ховаємо, коли сьогодні
    // вже входить у видимий проміжок — там кнопці нема куди вести.
    const today = root.querySelector('.schedule__today');
    if (today) {
      const now = toDateParam(new Date());
      today.hidden = now >= toDateParam(from) && now <= toDateParam(to);
    }
  };

  const applyFragment = (html) => {
    container.innerHTML = html;
    window._functions.applySlimSelect(container);
    window._functions.applyPhoneMask(container);
    revealNow(container);
  };

  // Перемикання днів/тижнів має лишатись швидким: юзер клікає стрілку
  // кілька разів поспіль, не чекаючи кожної відповіді. Новий fetch
  // скасовує попередній (той самий URL-стан однаково програє останньому
  // кліку), а не ставиться в чергу за ним і не ігнорується — DOM (href
  // стрілок, підпис) для наступного кліку вже оновлює updateBarNow вище,
  // синхронно, тож черга тут не потрібна.
  let pending = null;

  const fetchFragment = async (url, { pushState = true } = {}) => {
    pending?.abort();
    const controller = new AbortController();
    pending = controller;

    updateBarNow(url);
    // Адресний рядок — теж оптимістично, синхронно з кліком: pushState
    // усередині applyFragment оновлював його лише після відповіді fetch,
    // і при швидких повторних кліках URL вкладки відставав від того, що
    // юзер уже наклацав (History API не має власного «скасування» під
    // AbortController, тож writeState найкраще ставити тут, разом з
    // рештою оптимістичного стану, а не чекати на мережу). popstate
    // (кнопка «назад») уже сам змінив location — там pushState не потрібен,
    // інакше кожен «назад» дублював би той самий запис в історії.
    if (pushState) history.pushState({ schedule: true }, '', url);
    root.setAttribute('aria-busy', 'true');
    root.classList.add('schedule--loading');
    try {
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        signal: controller.signal,
      });
      if (!res.ok) { location.href = url; return; } // той самий GET, що спрацював би без JS
      applyFragment(await res.text());
    } catch (err) {
      if (err.name === 'AbortError') return; // скасовано новішим кліком — його finally прибере стан
      location.href = url; // мережа впала — повний перехід замість зламаного стану
    } finally {
      if (pending === controller) {
        root.removeAttribute('aria-busy');
        root.classList.remove('schedule--loading');
        pending = null;
      }
    }
  };

  // Усі посилання всередині #schedule ведуть у той самий календар з
  // іншими параметрами (вид/дата/скинути) — перехоплюємо їх усі одним
  // делегованим listener, а не список класів, який довелось би
  // синхронізувати з розміткою partials/schedule.php.
  root.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]');
    if (!a || !root.contains(a)) return;
    e.preventDefault();
    fetchFragment(a.href);
  });

  root.addEventListener('submit', (e) => {
    const form = e.target.closest('[data-schedule-filters]');
    if (!form) return;
    e.preventDefault();
    // location.pathname, не form.action: форма без атрибута action
    // (partials/schedule.php) резолвить .action по-різному залежно від
    // того, чи в URL уже є query string — location.pathname однозначний
    // і саме так GET-сабміт без JS будує ціль (той самий шлях, новий query).
    const qs = new URLSearchParams(new FormData(form));
    fetchFragment(`${location.pathname}?${qs}`);
  });

  // Вибір у селекті одразу застосовує фільтр — та сама поведінка, що
  // була до AJAX (form.submit() на change), тепер через requestSubmit():
  // він кидає справжню подію submit, яку ловить обробник вище, замість
  // дублювання fetch-логіки тут. Делегування, не listener на кожному
  // <select>: SlimSelect підміняє вигляд поля, але change кидає нативний
  // елемент, який лишається в DOM.
  root.addEventListener('change', (e) => {
    const select = e.target.closest('[data-schedule-filters] select');
    if (!select) return;
    select.form?.requestSubmit();
  });

  // Кнопка «назад»/«вперед» браузера: pushState вище лише міняє адресний
  // рядок, сам вміст назад не повертає — довантажуємо його тим самим шляхом.
  addEventListener('popstate', () => fetchFragment(location.href, { pushState: false }));
})();
