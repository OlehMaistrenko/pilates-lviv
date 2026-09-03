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

// Праймері-меню (фулскрін-меню). key => [label, href, children?]
// children — вкладений список [key => [label, href]] — друга колонка меню.
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
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu">
          <span class="nav-toggle__icon" aria-hidden="true"><i></i><i></i></span>
          <span class="nav-toggle__label">Меню</span>
        </button>

        <!-- Логотип — реальний файл із брендбуку. Дві копії, бо <img> не
             успадковує колір: над моховим героєм (і над відкритим меню)
             світла, на бежевій смузі темна. Двоколірний або перефарбований
             логотип брендбук забороняє, тому саме два готові файли, а не filter. -->
        <a class="brand" href="index.php" aria-label="Пілатес Львів — на головну">
          <img class="brand__logo brand__logo--light" src="assets/logo/pilates-lviv-light.svg" alt="Pilates Lviv" width="464" height="303">
          <img class="brand__logo brand__logo--dark" src="assets/logo/pilates-lviv-dark.svg" alt="" width="464" height="303">
        </a>

        <div class="site-header__actions">
          <a class="header-phone" href="<?= $contact['phone_href'] ?>">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
            <span class="header-phone__num"><?= $contact['phone'] ?></span>
          </a>
          <button type="button" class="btn btn--sm btn--ghost site-header__cta" data-modal="booking">Записатись</button>
        </div>
      </div>
    </header>
  </div>
  <?php if (!$header_over_hero): ?>
    <div class="header-spacer"></div>
  <?php endif; ?>

  <!-- Фулскрін-меню — одне на всі екрани. Хедер лишається поверх нього
       (z-index), тож лого й тумблер не дублюються всередині панелі. -->
  <div class="menu" id="menu" hidden>
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
        <div class="menu__contacts">
          <a href="<?= $contact['phone_href'] ?>"><?= $contact['phone'] ?></a>
          <a href="<?= $contact['map'] ?>" target="_blank" rel="noopener"><?= $contact['address'] ?></a>
        </div>
        <div class="menu__links">
          <a href="account.php">Кабінет клієнта</a>
          <a href="<?= $contact['instagram'] ?>" target="_blank" rel="noopener">Instagram</a>
          <a href="<?= $contact['facebook'] ?>" target="_blank" rel="noopener">Facebook</a>
        </div>
        <div class="lang-switch" role="group" aria-label="Мова сайту">
          <button type="button" class="lang-switch__btn is-active" data-lang="ua" aria-pressed="true">UA</button>
          <button type="button" class="lang-switch__btn" data-lang="en" aria-pressed="false">EN</button>
        </div>
      </div>
    </nav>
  </div>

  <main id="main">
