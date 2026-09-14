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
// Каталог напрямків — реальний, із діючого pilateslviv.com.
$primary = [
  'trainings' => ['Тренування', 'trainings.php', [
    'training-pilates-mat' => ['Пілатес', 'training-pilates-mat.php'],
    'training-reformer'    => ['Пілатес-Реформер', 'training-reformer.php'],
    'training-back'        => ['Здорова спина', 'training-back.php'],
    'training-personal'    => ['Персональне', 'training-personal.php'],
    'training-split'       => ['Спліт', 'training-split.php'],
    'academy'              => ['Навчальний центр', 'academy.php'],
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
          <details class="dropdown account">
            <summary class="btn btn--outlined btn--header btn--sm" aria-label="Кабінет клієнта">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-user"></use></svg>
              <span class="account__label">Ірина</span>
            </summary>
            <ul class="dropdown__panel">
              <li><a class="lang__opt" href="account.php">Кабінет клієнта</a></li>
              <li><a class="lang__opt" href="account.php?login">Увійти</a></li>
            </ul>
          </details>
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
