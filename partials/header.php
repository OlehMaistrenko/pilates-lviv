<?php
/**
 * Спільний хедер. Перед include задай:
 *   $nav        — ключ активного пункту меню (див. $primary нижче)
 *   $page_title — <title> сторінки
 * Обидві опційні. Хедер завжди fixed і лежить поверх контенту —
 * .header-spacer одразу після нього резервує відступ (--header-h).
 */
$nav = $nav ?? '';
$page_title = $page_title ?? 'Site';

// Праймері-меню (у хедері). key => [label, href, children?]
// children — вкладений список [key => [label, href]] для випадаючого підменю.
$primary = [
  'page-1' => ['Page 1', 'page-1.php'],
  'page-2' => ['Page 2', 'page-2.php', [
    'page-2-1' => ['Subpage 1', 'page-2-1.php'],
    'page-2-2' => ['Subpage 2', 'page-2-2.php'],
  ]],
  'page-3' => ['Page 3', 'page-3.php'],
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
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>

  <link rel="stylesheet" href="css/main.css">
  <?php if (!empty($page_css)): ?>
    <!-- CSS однієї сторінки ($page_css перед include). У <head>, не в кінці
         <body> як styles.css: власний CSS сторінки описує її перший екран,
         тобто критичний — deferred-лінк дав би блимання нестилізованого блоку. -->
    <link rel="stylesheet" href="<?= $page_css ?>">
  <?php endif; ?>
</head>

<body>
  <a class="skip-link" href="#main">Skip to content</a>

  <div class="header-fixed">
    <header class="site-header">
      <div class="site-header__row">
        <a class="brand" href="index.php" aria-label="Site — home">Site</a>

        <nav class="main-nav" aria-label="Primary navigation">
          <ul class="main-nav__list">
            <?php foreach ($primary as $k => $item) nav_link($k, $item, $nav); ?>
          </ul>
        </nav>

        <div class="site-header__actions">
          <div class="lang-switch" role="group" aria-label="Site language">
            <button type="button" class="lang-switch__btn is-active" data-lang="en" aria-pressed="true">EN</button>
            <button type="button" class="lang-switch__btn" data-lang="ua" aria-pressed="false">UA</button>
          </div>

          <button type="button" class="btn-icon btn-icon--sm search-toggle" aria-label="Search" aria-haspopup="dialog" aria-controls="search-modal">
            <svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-search"></use></svg>
          </button>

          <button class="nav-toggle" type="button" aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu">
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
          </button>
        </div>
      </div>
    </header>
  </div>
  <div class="header-spacer"></div>

  <!-- Мобільне меню (усі пункти) -->
  <div class="mobile-menu" id="mobile-menu" hidden>
    <div class="mobile-menu__backdrop"></div>
    <nav class="mobile-menu__panel" aria-label="Mobile navigation">
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
    </nav>
  </div>

  <!-- Пошук — Spotlight-стиль, ponytail: статичний список підказок, без реальної фільтрації.
       Enter у полі веде на search-results.php?q=... (нативний GET-сабміт форми) -->
  <div class="search-modal" id="search-modal" role="dialog" aria-modal="true" aria-label="Site search" data-lenis-prevent hidden>
    <div class="search-modal__backdrop" data-search-close></div>
    <div class="search-modal__panel">
      <form class="search-modal__input-row" action="search-results.php" method="get">
        <svg class="icon search-modal__icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-search"></use></svg>
        <input type="text" class="search-modal__input" id="search-input" name="q" placeholder="Search the site…" autocomplete="off">
        <kbd class="search-modal__esc">esc</kbd>
        <button type="button" class="btn-icon btn-icon--sm search-modal__close" data-search-close aria-label="Close search">
          <svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-close"></use></svg>
        </button>
      </form>
      <div class="search-modal__suggestions">
        <!-- групи підказок (label + список) — приклад структури, наповнюється на проєкті -->
      </div>
    </div>
  </div>

  <main id="main">
