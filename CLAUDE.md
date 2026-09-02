# Заготовка — кодстайл та дизайн-система

## Стек

- Vanilla HTML / CSS / JS — без фреймворків, без білдтулів
- **PHP-інклуди** для спільного лейауту та секцій — сторінки мають розширення `.php`,
  повторювані блоки живуть у `partials/` і підключаються через `include`.
  Сайт треба віддавати PHP-сервером (`php -S localhost:8000`), не `file://`.
- CSS — два файли, за принципом critical/deferred (PageSpeed):
  - `css/main.css` — підключається `<link>` у `<head>`, render-blocking
    навмисно. Токени, reset, глобальні utility (text/spacing/reveal/label/
    section-head), layout (container/section), хедер+nav+мобільне меню/пошук
    (розмітка й тумблери), skip-link.
  - `css/styles.css` — підключається `<link>` у самому кінці `<body>`,
    перед `<script src="js/main.js">`. Усе нижче першого екрана: секції
    сторінок, `.simple-text`, accordion, футер-скелет, повна стилізація
    модалок/мобільного меню/пошуку (вони мають `hidden` у розмітці —
    браузер ховає їх ще до завантаження CSS, тож deferred-стилі не
    спричиняють блимання).
  - Вендорські стилі (Swiper/Mapbox — гейт `$vendor_swiper`/`$vendor_map`
    перед `include footer.php`) підключаються так само в кінці `<body>`,
    **перед** `styles.css`
  - Нову секцію нижче першого екрана — завжди в `styles.css`. У `main.css`
    йде лише те, що видиме одразу на завантаженні (хедер/глобальний
    utility) — за замовчуванням вважай нову секцію deferred, якщо не
    впевнений.
- Один JS-файл: `js/main.js`
- Партіали (хедер, футер, повторювані секції, модалки): `partials/`

---

### Spacing scale

One spacing scale, defined once in the `:root` block of `css/main.css`. Never add a parallel token (`--gap-*`, `--pad-*`, etc.) that duplicates a value the scale already covers — if a component needs "a medium gap," reuse the closest existing step instead of inventing a named alias for it.

- Small, fine-grained steps (button/pill padding, icon gaps) can stay fixed `rem` — a fluid range on a value that's only a few px has no visible effect and isn't worth the complexity.
- Larger, layout-rhythm steps (section gaps, card gaps, margins between blocks) should be fluid (`clamp()`), so mobile doesn't inherit desktop-sized gaps. Interpolate linearly between a mobile size and a desktop size across a min/max viewport range (the [Utopia](https://utopia.fyi/type/calculator/) method) rather than a naive `clamp(min, Xvw, max)` guess — the naive form isn't anchored to real viewport widths and drifts unpredictably between breakpoints.
- The desktop end of the range must equal the step's current/intended desktop value — desktop should render exactly as before; only the mobile floor is new.
- Section-level tokens (container padding, section padding) follow the same fluid-scale principle and already exist — don't reintroduce a separate spacing system alongside them.
- Adding a genuinely new step is fine; adding a same-magnitude token under a different name is the thing to avoid.

## Структура файлів

```
index.php               — головна (порожній каркас: include header + footer)
partials/
  header.php             — <head>, хедер, nav, мобільне меню, пошук
  footer.php             — закриває <main>, футер, модалка-оболонка, main.js
  modals/
    loader.php            — AJAX-ендпоінт, whitelist фрагментів
    example.php           — приклад фрагмента модалки
css/main.css             — critical CSS (<head>): токени, reset, utility, хедер
css/styles.css           — deferred CSS (кінець <body>): секції, модалки, футер
js/main.js               — reveal, motion (Lenis+GSAP), header-state, меню, tabs, accordion
assets/                  — зображення, іконки (icons/sprite.svg)
css/vendor/, js/vendor/  — swiper, mapbox, gsap, lenis (підключаються за гейтом)
```

Кольорову палітру й шрифти для конкретного проєкту додавай у `:root`
(`css/main.css`) і `@font-face` там само — заготовка навмисно тримає лише
нейтральні `--c-page`/`--c-ink` і `system-ui`.

---

## Брейкпоінти

```
1080px   планшет / малий десктоп
 768px   мобільний
 560px   дрібні форми/модалки
```

---

### .simple-text (WP rich text)

Блок для довільного HTML з CMS. Підтримує: `p h2 h3 h4 ul ol li blockquote cite figure figcaption img table thead tbody tr th td hr strong em a video iframe`.

Для відео-embed обов'язково обгортати:

```html
<div class="simple-text__video-wrap"><iframe src="..."></iframe></div>
```

Для `table` обов'язково обгортати (горизонтальний скрол, якщо колонки не влазять):

```html
<div class="simple-text__table-wrap">
  <table>
    ...
  </table>
</div>
```

---

## CSS — структура файлу

Блоки розділені коментарями:

```css
/* ============================================================
   Назва блоку
   ============================================================ */
```

Порядок блоків у `css/main.css`:

1. Токени `:root`
2. Reset & base
3. Utility (icon, text, spacing, reveal, label, section-head)
4. Layout (container, section)
5. Skip-link
6. Header + main nav

Порядок блоків у `css/styles.css`:

1. Modal overlay, Form, мобільне меню, пошук (deferred — приховані `hidden`)
2. `.simple-text`, Accordion
3. Секції сторінок у порядку появи
4. Footer

**Респонсів — одразу під десктопними правилами свого блоку**, не в одному
загальному `Responsive`-розділі внизу файлу. Кожен `@media` для секції/компонента
йде одразу після її десктопних правил, у межах того самого named-блоку — щоб
усі стилі одного компонента (десктоп + адаптив) читались одним шматком, без
стрибків у кінець файлу. Виняток — спільні utility-брейкпоінти (напр.
`.mt-md-*`), які й так живуть при своєму utility-блоку.

---

## Типографіка — дисципліна

Консистентність важливіша за різноманіття. Жорсткі правила:

- **Розмір тексту** — тільки через токени `--fs-*` (`--fs-xs … --fs-mega`).
  Жодних ad-hoc `rem`/`px` для `font-size`.
- **Розмір і колір довільного тексту** (лід-абзаци, підписи, другорядний текст)
  — через utility-класи `.text--xs/--sm/--body/--lead` (розмір) і
  `.text--muted` (колір), а не власний `font-size`/`color` у block-класі.
  Block-клас лишає собі тільки лейаут (`max-width`, `margin`, `display`).
  Заголовки (H1–H3, `section-head__title` тощо) і mono-лейбли під це правило
  не підпадають — там власні класи.
- **Насиченість** — фіксовані ваги `--fw-reg` (400), `--fw-med` (500),
  `--fw-sb` (600). Скільки саме гарнітур і які ваги вони фізично мають —
  рішення проєкту; не додавай вагу, якої немає у файлі шрифту.
- **Великі заголовки** — `line-height: var(--lh-display)` +
  `letter-spacing: var(--tracking-tight)`.
- **Лейбли/лічильники** — utility-клас `.label`: uppercase,
  `letter-spacing: var(--tracking-mono)`, `--fs-xs`.
- **Гарнітури** — рівно дві ролі: `--font-display` (заголовки, лейбли,
  кнопки) і `--font-body` (тіло, підписи). Заготовка тримає обидві як
  `system-ui` — проєкт підставляє свої `@font-face` в `css/main.css`.

---

## Правила написання коду

- **Класи** — BEM-inspired: `.block`, `.block__element`, `.block--modifier`
- **Нові класи** — тільки якщо немає підходящого існуючого (перевіряй `label`,
  `reveal`, `section-head`, `text`, `container`, `simple-text`, `accordion`,
  `modal-overlay`)
- **Дизайн-компоненти бренду** (кнопки, картки, плашки, теги тощо) у заготовці
  немає навмисно — додавай їх на конкретному проєкті разом із палітрою і
  шрифтами, а не тягни зі старого сайту.
- **Контейнери** — `.container` (1760px, дефолт), `.container--narrow` (1120px,
  довгі тексти), `.container--full` (без ліміту: герой, CTA, панелі, полотна).
- **Hover-стани** — тільки на клікабельних елементах (`a`, `button` тощо).
  Некліковані елементи (звичайні картки, списки, текстові блоки) hover не
  отримують — це фальшивий сигнал інтерактивності.
- **Коментарі** — тільки на те, чого не видно з коду: _чому_ так, а не _що_
  тут написано. Коментар, який переказує сусідній рядок, не пишемо взагалі.
  Пишемо, коли є неочевидна причина: обхід баґу вендора, порядок, від якого
  щось залежить, свідома відмова від очевиднішого рішення, зв'язок із кодом
  в іншому файлі. Іменований блок-коментар секції (шапка `/* === Назва === */`)
  під це правило не підпадає — він для навігації.
- **Інлайн-стилі** — тільки для CSS-змінних що варіюються (`style="--tag-c: #b8dee0"`)
- **Зображення** — завжди `loading="lazy"` крім першого above-the-fold (`loading="eager"`)
- **Іконки** — іконка, що повторюється в кількох місцях, не inline-copy-paste
  на кожному місці, а `<symbol>` у `assets/icons/sprite.svg`, підключається
  через `<svg class="icon"><use href="assets/icons/sprite.svg#icon-name"></use></svg>`.
- **Анімації** — тільки через `transform`/`opacity`/`clip-path`, нічого що
  тригерить layout. Прості reveal-и — наявний `[data-reveal]`
  (IntersectionObserver + CSS, дешево для PageSpeed). GSAP ScrollTrigger — лише
  там, де потрібен scrub або pin: `data-anim="words|pin|parallax|zoom"`.
  Вендори (Lenis/GSAP) підключаються за гейтом `$vendor_motion`, `defer`.
- **JS** — vanilla, IIFE для локальної логіки, вже підключений `main.js` бере на себе все стандартне
- **Нові inline-скрипти** — тільки якщо логіка специфічна для однієї сторінки
- **Семантика** — `<article>` для новин, `<aside>` для сайдбару, `<nav>` для навігацій, `<figure>` для зображень з підписом

!!! не запускай сервер для перевірки. Я перевірятиму сам вручну

---

## Хедер / футер

Не копіювати — підключати через `include 'partials/header.php'` /
`include 'partials/footer.php'`. Активний пункт меню задається змінною `$nav`
**перед** `include` (ключі — по `$primary` у `header.php`); `header.php` сам
проставляє `aria-current="page"`. Меню (`$primary`), лого/бренд, колонки
футера — генеричні заглушки, замінюються на проєкті.

Модалки — спільна оболонка `.modal-overlay` (footer.php) + AJAX-фрагменти
з `partials/modals/*.php`, whitelist у `partials/modals/loader.php`. Тригер —
будь-який елемент з `data-modal="name"` (або `data-modal="name?query=..."`).

Акордеони — нативний `<details>`-подібний патерн через клас `.accordion` +
`.accordion__summary`/`.accordion__body`, стан і ексклюзивність (`data-accordion-group`)
рахує `js/main.js`.
