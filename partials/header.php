<?php
/**
 * Спільний хедер. Перед include задай:
 *   $nav               — ключ активного пункту меню (див. $primary нижче)
 *   $page_title        — <title> сторінки
 *   $page_description  — <meta name="description">
 *   $header_over_hero  — true на сторінках, що починаються з мохового героя:
 *                        хедер стає прозорим і лягає ПОВЕРХ героя, тому
 *                        .header-spacer не друкується.
 * Усі опційні.
 */
$nav = $nav ?? '';
$page_title = $page_title ?? 'Студія «Пілатес Львів»';
$page_description = $page_description ?? '';
$header_over_hero = $header_over_hero ?? false;

// Контакти студії — одне джерело правди на весь сайт (хедер, футер, модалки)
$contact = [
  'phone'      => '+38 (063) 015-05-17',
  'phone_href' => 'tel:+380630150517',
  'address'    => 'м. Львів, вул. Б. Романицького, 24а',
  'map'        => 'https://maps.google.com/?q=Львів,+вулиця+Богдана+Романицького,+24а',
  'instagram'  => 'https://instagram.com/pilates_lviv',
  'facebook'   => 'https://facebook.com/pilateslviv',
];

// Праймері-меню (у хедері). key => [label, href, children?]
// children — вкладений список [key => [label, href]] для випадаючого підменю.
// Каталог напрямків — реальний, із діючого pilateslviv.com.
$primary = [
  'trainings' => ['Тренування', 'trainings.php', [
    'training-pilates'  => ['Пілатес Springtone', 'training-pilates.php'],
    'training-yoga'     => ['Йога', 'training-yoga.php'],
    'training-recovery' => ['Функціональне відновлення', 'training-recovery.php'],
    'training-dance'    => ['Танці', 'training-dance.php'],
    'training-physio'   => ['Консультація фізіолога', 'training-physio.php'],
    'academy'           => ['Навчальний центр', 'academy.php'],
  ]],
  'schedule'  => ['Розклад', 'schedule.php'],
  'prices'    => ['Ціни', 'prices.php'],
  'locations' => ['Локації', 'locations.php', [
    'location-bryukhovychi' => ['Брюховичі', 'location-bryukhovychi.php'],
    'location-chuprynky'    => ['Чупринки', 'location-chuprynky.php'],
    'location-sykhiv'       => ['Сихів', 'location-sykhiv.php'],
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

/** Друкує <li><a> пункту, з вкладеним підменю (якщо є) і aria-current, якщо активний.
 *  Батько групи лишається підсвіченим і на сторінках-дітях — але через клас
 *  .is-active, а не aria-current: «поточна сторінка» в дереві рівно одна. */
function nav_link(string $key, array $item, string $active): void {
  [$label, $href] = $item;
  $children = $item[2] ?? null;
  $cur = $key === $active ? ' aria-current="page"' : '';

  if (!$children) {
    echo '<li><a href="' . $href . '"' . $cur . '>' . $label . '</a></li>';
    return;
  }

  $cls = ($cur || isset($children[$active])) ? ' class="is-active"' : '';
  echo '<li class="main-nav__item has-children">';
  echo '<a href="' . $href . '"' . $cls . $cur . '>' . $label . '<i class="main-nav__caret" aria-hidden="true"></i></a>';
  echo '<ul class="main-nav__submenu">';
  foreach ($children as $ck => $citem) {
    [$clabel, $chref] = $citem;
    $ccur = $ck === $active ? ' aria-current="page"' : '';
    echo '<li><a href="' . $chref . '"' . $ccur . '>' . $clabel . '</a></li>';
  }
  echo '</ul>';
  echo '</li>';
}
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

  <div class="header-fixed<?= $header_over_hero ? ' header-fixed--over' : '' ?>">
    <header class="site-header">
      <div class="site-header__row">
        <!-- Логотип — реальний файл із брендбуку. Дві копії, бо <img> не
             успадковує колір: над моховим героєм світла, на бежевій смузі
             темна. Двоколірний або перефарбований логотип брендбук забороняє,
             тому саме два готові файли, а не filter. -->
        <a class="brand" href="index.php" aria-label="Пілатес Львів — на головну">
          <img class="brand__logo brand__logo--light" src="assets/logo/pilates-lviv-light.svg" alt="Pilates Lviv" width="464" height="303">
          <img class="brand__logo brand__logo--dark" src="assets/logo/pilates-lviv-dark.svg" alt="" width="464" height="303">
        </a>

        <nav class="main-nav" aria-label="Основна навігація">
          <ul class="main-nav__list">
            <?php foreach ($primary as $k => $item) nav_link($k, $item, $nav); ?>
          </ul>
        </nav>

        <div class="site-header__actions">
          <div class="lang-switch" role="group" aria-label="Мова сайту">
            <button type="button" class="lang-switch__btn is-active" data-lang="ua" aria-pressed="true">UA</button>
            <button type="button" class="lang-switch__btn" data-lang="en" aria-pressed="false">EN</button>
          </div>

          <a class="header-phone" href="<?= $contact['phone_href'] ?>">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
            <span class="header-phone__num"><?= $contact['phone'] ?></span>
          </a>

          <button type="button" class="btn btn--sm btn--umber site-header__cta" data-modal="booking">Записатись</button>

          <a class="btn-icon btn-icon--sm" href="account.php" aria-label="Кабінет клієнта">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-user"></use></svg>
          </a>

          <button class="nav-toggle" type="button" aria-label="Меню" aria-expanded="false" aria-controls="mobile-menu">
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
          </button>
        </div>
      </div>
    </header>
  </div>
  <?php if (!$header_over_hero): ?>
    <div class="header-spacer"></div>
  <?php endif; ?>

  <!-- Мобільне меню (усі пункти) -->
  <div class="mobile-menu" id="mobile-menu" hidden>
    <div class="mobile-menu__backdrop"></div>
    <nav class="mobile-menu__panel" aria-label="Мобільна навігація">
      <ul class="mobile-menu__list">
        <?php foreach ($primary as $k => $item):
          [$label, $href] = $item;
          $children = $item[2] ?? null;
        ?>
          <li>
            <a href="<?= $href ?>"<?= $children && isset($children[$nav]) ? ' class="is-active"' : '' ?><?= $k === $nav ? ' aria-current="page"' : '' ?>><?= $label ?></a>
            <?php if ($children): ?>
              <ul class="mobile-menu__submenu">
                <?php foreach ($children as $ck => $citem): [$clabel, $chref] = $citem; ?>
                  <li><a href="<?= $chref ?>"<?= $ck === $nav ? ' aria-current="page"' : '' ?>><?= $clabel ?></a></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="mobile-menu__foot">
        <a class="btn btn--sand" href="<?= $contact['phone_href'] ?>">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
          <?= $contact['phone'] ?>
        </a>
        <a class="mobile-menu__account" href="account.php">Кабінет клієнта</a>
        <p class="mobile-menu__addr"><?= $contact['address'] ?></p>
      </div>
    </nav>
  </div>

  <main id="main">
