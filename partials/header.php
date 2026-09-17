<?php
/**
 * Спільний хедер. Перед include задай:
 *   $nav               — ключ активного пункту меню (див. $primary нижче)
 *   $page_title        — <title> сторінки
 *   $page_description  — <meta name="description">
 *   $header_over_hero  — true на сторінках, що починаються з мохового героя:
 *                        хедер стає прозорим і лягає ПОВЕРХ героя, тому
 *                        .header-spacer не друкується.
 *   $header_theme      — 'light' (за замовчуванням) | 'dark'. Колір хедера
 *                        на сторінках БЕЗ героя (header_over_hero=false),
 *                        де перший екран сам темний (напр. мохова секція
 *                        одразу під хедером) — 'dark' тримає бежевий
 *                        текст/контроли на такому фоні.
 * Усі опційні.
 */
/* AJAX-фрагмент розкладу (js/schedule.js) шле той самий GET на ту саму
   сторінку, тільки з X-Requested-With. Клієнт кладе тіло відповіді в
   .container календаря, тож усе, що сторінка друкує ДО #schedule (хедер,
   банер, секції над ним), потрапило б туди разом із фрагментом — і
   сторінка дублювалась би всередині себе. Буферизуємо все з цього місця;
   partials/schedule.php сам викине буфер перед друком фрагмента й вийде.
   Гейт тут, а не в кожній сторінці-споживачі: інакше його треба памʼятати
   щоразу, коли розклад зʼявляється на новій детальній сторінці. */
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') ob_start();

$nav = $nav ?? '';
$page_title = $page_title ?? 'Студія «Пілатес Львів»';
$page_description = $page_description ?? '';
$header_over_hero = $header_over_hero ?? false;
$header_theme = $header_theme ?? 'light';

// Контакти студії — одне джерело правди на весь сайт (хедер, футер, модалки)
$contact = [
  'phone'      => '+38 (063) 015-05-17',
  'phone_href' => 'tel:+380630150517',
  'address'    => 'м. Львів, вул. Б. Романицького, 24а',
  'map'        => 'https://maps.google.com/?q=Львів,+вулиця+Богдана+Романицького,+24а',
  'instagram'  => 'https://instagram.com/pilates_lviv',
  'facebook'   => 'https://facebook.com/pilateslviv',
  // 3 локації студії. Чупринки — реальні дані з діючого сайту; Брюховичі
  // й Сихів — плейсхолдер, контакти для них ще треба отримати від клієнта.
  // hall — id зали в InstaSport (api/schedule-mock.json → _refs.halls), ним
  // фільтрує розклад і модалка запису. lat/lng Брюховичів і Сихова —
  // приблизні (за плейсхолдерними адресами), уточнити разом з адресами.
  'locations'  => [
    ['slug' => 'chuprynky',    'hall' => 1, 'lat' => 49.8317, 'lng' => 24.0129, 'label' => 'Чупринки',  'address' => 'м. Львів, вул. Б. Романицького, 24а', 'phone' => '+38 (063) 015-05-17', 'phone_href' => 'tel:+380630150517'],
    ['slug' => 'bryukhovychi', 'hall' => 2, 'lat' => 49.8987, 'lng' => 23.9610, 'label' => 'Брюховичі', 'address' => 'м. Львів, вул. Сагайдачного, 7',       'phone' => '+38 (063) 015-05-17', 'phone_href' => 'tel:+380630150517'],
    ['slug' => 'sykhiv',       'hall' => 3, 'lat' => 49.7919, 'lng' => 24.0575, 'label' => 'Сихів',     'address' => 'м. Львів, просп. Червоної Калини, 62', 'phone' => '+38 (063) 015-05-17', 'phone_href' => 'tel:+380630150517'],
  ],
];

// Праймері-меню (фулскрін-меню). key => [label, href, children?]
// children — вкладений список [key => [label, href]] — друга колонка меню.
// Каталог напрямків: Реформер/Персональне/Спліт — реальні, з діючого
// pilateslviv.com; Ознайомче/Кадилак/Wunda Chair і Barrel — формати за
// зразком секції WHAT WE DO на exhalepilateslondon.com, TODO уточнити з
// клієнтом. Навчальний центр тут же — Події й Співпраця (теж пункти
// WHAT WE DO) уже мають свій пункт нижче, у «Про нас».
$primary = [
  'trainings' => ['Тренування', 'trainings.php', [
    'training-intro'        => ['Ознайомче заняття', 'training-detail.php?training=intro'],
    'training-reformer'     => ['Пілатес-Реформер', 'training-detail.php?training=reformer'],
    'training-cadillac'     => ['Кадилак', 'training-detail.php?training=cadillac'],
    'training-chair-barrel' => ['Wunda Chair і Barrel', 'training-detail.php?training=chair-barrel'],
    'training-personal'     => ['Персональне', 'training-detail.php?training=personal'],
    'training-split'        => ['Спліт', 'training-detail.php?training=split'],
    'academy'               => ['Навчальний центр', 'academy.php'],
  ]],
  'schedule'  => ['Розклад', 'schedule.php'],
  'prices'    => ['Ціни', 'prices.php'],
  'locations' => ['Локації', 'locations.php', [
    'location-chuprynky'    => ['Чупринки', 'location-single.php?loc=chuprynky'],
    'location-bryukhovychi' => ['Брюховичі', 'location-single.php?loc=bryukhovychi'],
    'location-sykhiv'       => ['Сихів', 'location-single.php?loc=sykhiv'],
  ]],
  'team'      => ['Команда', 'team.php'],
  'about'     => ['Про нас', 'about.php', [
    'about'       => ['Про студію', 'about.php'],
    'events'      => ['Події', 'events.php'],
    'partnership' => ['Співпраця', 'partnership.php'],
    'blog'        => ['Блог', 'blog.php'],
    'faq'         => ['Питання та відповіді', 'faq.php'],
  ]],
  'contacts'  => ['Контакти', 'contacts.php'],
];

?>
<!DOCTYPE html>
<html lang="uk">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <?php if ($page_description): ?>
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
  <?php endif; ?>

  <link rel="icon" href="assets/logo/pilates-lviv-mark.svg" type="image/svg+xml">

  <!-- Один файл, одна гарнітура: кириличний сабсет Geologica несе і
       заголовок героя, і весь інтерфейс. Без preload браузер знайде його
       аж після парсингу CSS, і перший екран встигне блимнути системним
       шрифтом. Латинський сабсет preload не потребує — латиниці на
       першому екрані майже немає. -->
  <link rel="preload" href="assets/fonts/geologica-cyrillic.woff2" as="font" type="font/woff2" crossorigin>

  <link rel="stylesheet" href="css/main.css">
  <?php if (!empty($page_css)): ?>
    <!-- CSS однієї сторінки ($page_css перед include). У <head>, не в кінці
         <body> як styles.css: власний CSS сторінки описує її перший екран,
         тобто критичний — deferred-лінк дав би блимання нестилізованого блоку. -->
    <link rel="stylesheet" href="<?= $page_css ?>">
  <?php endif; ?>
</head>

<body>
  <a class="skip-link" href="#main">Перейти до вмісту</a>

  <?php
  // Маркетингова полоска над шапкою. TODO: приклад акції, узгодити з клієнтом.
  $topbar_text   = 'Знижка 20% на перший абонемент для нових клієнтів — дізнатися умови';
  $topbar_href   = 'prices.php';
  $topbar_label  = 'Знижка 20% на перший абонемент для нових клієнтів — дізнатися умови';
  // Запас копій тексту в стрічці: на широкому екрані чи з короткою фразою
  // двох копій може не вистачити на всю ширину — за одним проходом
  // анімації буде видно порожнє місце. 8 — запас, що покриває будь-яку
  // ширину вʼюпорта для фрази такої довжини.
  $topbar_repeat = 8;
  ?>
  <!-- Без hidden/кнопки закриття — ховається/повертається разом зі
       стиском хедера (--topbar-h у css/main.css), той самий скрол-тригер,
       що й сам хедер. Уся смуга — посилання; текст дублюємо для безшовної
       бігучої стрічки й ховаємо дублікати від скрінрідера, сенс — у
       aria-label самого посилання. -->
  <a class="topbar" href="<?= htmlspecialchars($topbar_href) ?>" aria-label="<?= htmlspecialchars($topbar_label) ?>">
    <div class="topbar__track" aria-hidden="true" style="--topbar-repeat: <?= (int) $topbar_repeat ?>">
      <?php for ($i = 0; $i < $topbar_repeat; $i++): ?>
        <span class="topbar__text"><?= htmlspecialchars($topbar_text) ?></span>
      <?php endfor; ?>
    </div>
  </a>

  <div class="header-fixed<?= $header_over_hero ? ' header-fixed--over' : '' ?><?= (!$header_over_hero && $header_theme === 'dark') ? ' header-fixed--dark' : '' ?>">
    <header class="site-header">
      <div class="site-header__row">
        <div class="site-header__lead">
          <button class="btn btn--outlined btn--header btn--sm nav-toggle" type="button" aria-expanded="false" aria-controls="menu">
            <span class="nav-toggle__icon" aria-hidden="true"><i></i><i></i></span>
            <span class="nav-toggle__label">Меню</span>
          </button>

          <!-- Нативний <details>: відкриття/закриття, клавіатура і роль
               кнопки — від браузера. JS (js/main.js) додає лише закриття
               кліком повз і по Escape, чого <details> сам не вміє. -->
          <details class="lang dropdown">
            <summary class="btn btn--outlined btn--header btn--sm" aria-label="Мова сайту: українська">
              UA<i class="dropdown__caret" aria-hidden="true"></i>
            </summary>
            <ul class="lang__list dropdown__panel">
              <li><button type="button" class="lang__opt is-active" data-lang="ua" aria-current="true">Українська</button></li>
              <li><button type="button" class="lang__opt" data-lang="en">English</button></li>
            </ul>
          </details>
        </div>

        <!-- Логотип — звичайні <img>, файли з адмінки. Дві копії, бо <img>
             не приймає колір через CSS: над моховим героєм (і над відкритим
             меню) світла, на бежевій смузі темна. -->
        <a class="brand" href="index.php" aria-label="Пілатес Львів — на головну">
          <img class="brand__logo brand__logo--light" src="assets/logo/pilates-lviv-light.svg" alt="Pilates Lviv" width="464" height="303">
          <img class="brand__logo brand__logo--dark" src="assets/logo/pilates-lviv-dark.svg" alt="" width="464" height="303">
        </a>

        <div class="site-header__actions">
          <a class="btn btn--outlined btn--header btn--sm booking-btn" href="schedule.php" aria-label="Записатись">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-calendar"></use></svg>
            <span class="booking-btn__label" aria-hidden="true">Записатись</span>
          </a>

          <?php
          // Стан кабінету в хедері. api/account.php підключається тільки тут,
          // бо це єдине місце в шапці, яке про клієнта знає; require_once —
          // сторінки кабінету підключають його й самі.
          require_once __DIR__ . '/../api/account.php';
          $hdr_logged  = account_is_logged();
          $hdr_profile = $hdr_logged ? account_profile() : null;
          // Тільки імʼя, без прізвища: у кнопку хедера довгий рядок не влазить.
          $hdr_name = $hdr_profile ? explode(' ', trim($hdr_profile['name']))[0] : '';
          // Активний пункт — $account_nav уже задають самі сторінки кабінету
          // (account.php тощо) до цього include, той самий принцип, що $nav
          // для основного меню. На сторінках поза кабінетом просто немає.
          $hdr_account_nav = $account_nav ?? '';
          // Ті самі назви й порядок, що в partials/account/nav.php
          // (бокова навігація кабінету) — один список пунктів, дві точки
          // входу.
          $hdr_account_menu = [
            'profile'  => ['Персональна інформація', 'account.php'],
            'cards'    => ['Абонементи', 'account-cards.php'],
            'visits'   => ['Мої заняття', 'account-visits.php'],
            'deposits' => ['Рахунок і поповнення', 'account-deposits.php'],
            'password' => ['Зміна паролю', 'account-password.php'],
          ];
          ?>
          <?php if ($hdr_logged): ?>
            <details class="dropdown account">
              <summary class="btn btn--outlined btn--header btn--sm" aria-label="Кабінет клієнта">
                <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-user"></use></svg>
                <span class="account__label"><?= htmlspecialchars($hdr_name) ?></span>
              </summary>
              <ul class="dropdown__panel">
                <?php foreach ($hdr_account_menu as $key => [$label, $href]): ?>
                  <?php $is_current = $key === $hdr_account_nav; ?>
                  <li><a class="lang__opt<?= $is_current ? ' is-active' : '' ?>" href="<?= $href ?>"<?= $is_current ? ' aria-current="page"' : '' ?>><?= $label ?></a></li>
                <?php endforeach; ?>
                <li>
                  <!-- Та сама форма-вихід, що в сайдбарі кабінету
                       (partials/account/nav.php) — на мобілці сайдбар
                       прибирається, тож вихід з будь-якої сторінки має бути
                       доступний саме тут. -->
                  <form class="dropdown__logout" action="" method="post" data-remote>
                    <button type="submit" class="lang__opt dropdown__logout-btn">Вийти</button>
                  </form>
                </li>
              </ul>
            </details>
          <?php else: ?>
            <button type="button" class="btn btn--outlined btn--header btn--sm account" data-modal="auth" aria-label="Увійти в кабінет">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-user"></use></svg>
              <span class="account__label">Увійти</span>
            </button>
          <?php endif; ?>
        </div>
      </div>
    </header>
  </div>
  <?php if (!$header_over_hero): ?>
    <div class="header-spacer"></div>
  <?php endif; ?>

  <!-- Фулскрін-меню — одне на всі екрани. Хедер лишається поверх нього
       (z-index), тож лого й тумблер не дублюються всередині панелі. -->
  <div class="menu" id="menu" data-lenis-prevent hidden>
    <nav class="menu__panel container" aria-label="Основна навігація">
      <ul class="menu__list">
        <?php $i = 0; foreach ($primary as $k => $item): [$label, $href] = $item; $children = $item[2] ?? null; ?>
          <li style="--i: <?= $i++ ?>">
            <a href="<?= $href ?>"<?= $children && isset($children[$nav]) ? ' class="is-active"' : '' ?><?= $k === $nav ? ' aria-current="page"' : '' ?>><?= $label ?></a>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="menu__groups">
        <?php foreach ($primary as $k => $item): if (empty($item[2])) continue; [$label, $href, $children] = $item; ?>
          <div class="menu__group" style="--i: <?= $i++ ?>">
            <a class="label" href="<?= $href ?>"><?= $label ?></a>
            <ul>
              <?php foreach ($children as $ck => $citem): [$clabel, $chref] = $citem; ?>
                <li><a href="<?= $chref ?>"<?= $ck === $nav ? ' aria-current="page"' : '' ?>><?= $clabel ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="menu__foot" style="--i: <?= $i ?>">
        <div class="menu__locations">
          <?php foreach ($contact['locations'] as $loc): ?>
            <div class="menu__location">
              <a href="<?= $loc['phone_href'] ?>"><?= $loc['phone'] ?></a>
              <a href="<?= $contact['map'] ?>" target="_blank" rel="noopener"><?= $loc['address'] ?></a>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="menu__links">
          <a class="btn btn--icon btn--sm btn--outlined btn--light" href="<?= $contact['instagram'] ?>" target="_blank" rel="noopener" aria-label="Instagram">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-instagram"></use></svg>
          </a>
          <a class="btn btn--icon btn--sm btn--outlined btn--light" href="<?= $contact['facebook'] ?>" target="_blank" rel="noopener" aria-label="Facebook">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-facebook"></use></svg>
          </a>
        </div>
      </div>
    </nav>
  </div>

  <main id="main">
