<?php
$nav = 'locations';

// Дані локації (адреса, hall id, slug) — з $contact['locations'] у header.php;
// тут лише кадри: один набір assets/img/location-1, поки фото Брюховичів
// і Сихова не отримані від клієнта.
$loc_photos = [
  'chuprynky'    => [1, 2, 3, 4],
  'bryukhovychi' => [5, 6, 7, 8],
  'sykhiv'       => [3, 5, 1, 7],
];
$loc_slug = $_GET['loc'] ?? '';
if (!isset($loc_photos[$loc_slug])) $loc_slug = 'chuprynky';   // невідомий слаг у URL — фолбек на першу локацію

include 'partials/header.php';   // $contact вже несе header.php — тягнемо тут, дані локації нижче

$loc    = current(array_filter($contact['locations'], fn($l) => $l['slug'] === $loc_slug));
$hall   = $loc['hall'];
$photos = $loc_photos[$loc_slug];

$page_title = $loc['label'] . ' — локації студії «Пілатес Львів»';
$page_description = 'Пілатес-студія на ' . $loc['label'] . ': ' . $loc['address'] . '. Розклад, тренери, запис на тренування.';
?>

<!-- ============================================================
     01 · Банер — фото зали цієї локації
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/location-1/<?= $photos[0] ?>.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <a href="locations.php">Локації</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page"><?= htmlspecialchars($loc['label']) ?></span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines"><?= mb_strtoupper($loc['label']) ?></h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          <?= htmlspecialchars($loc['address']) ?>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Контакти локації — адреса/телефон/карта, той самий .icon-link,
     що в футері; тут же кнопка запису на цій локації
     ============================================================ -->
<section class="section section--tight-top">
  <div class="container container--narrow">
    <div class="text--sm text--muted mb-3">Загальна інформація</div>
    <ul class="mb-6" style="display:flex; flex-direction:column; gap: var(--sp-3);">
      <li>
        <a class="icon-link" href="<?= $contact['map'] ?>" target="_blank" rel="noopener">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
          <?= htmlspecialchars($loc['address']) ?>
        </a>
      </li>
      <li>
        <a class="icon-link" href="<?= $loc['phone_href'] ?>">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
          <?= htmlspecialchars($loc['phone']) ?>
        </a>
      </li>
    </ul>

    <div class="split__actions" data-reveal>
      <a class="btn btn--filled" href="schedule.php?location=<?= $hall ?>">Розклад на цій локації</a>
      <button type="button" class="btn btn--outlined" data-modal="callback">Замовити дзвінок</button>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Галерея зали — той самий свайпер, що на головній/тренері
     ============================================================ -->
<?php
$gallery_title = 'Зала на ' . $loc['label'];
$gallery_items = array_map(fn($p) => ["location-1/$p.jpg", 'Зала студії, ' . $loc['label']], $photos);
$vendor_swiper = true;
include 'partials/gallery.php';
?>

<!-- ============================================================
     04 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Записатись на ' . $loc['label'];
$cta_text  = 'Перше заняття — знайомство: тренер подивиться, як ви рухаєтесь, і підбере формат.';
$cta_modal = 'booking?location=' . $hall;
include 'partials/cta.php';
?>

<!-- ============================================================
     05 · FAQ — про цю локацію
     ============================================================ -->
<?php
$faq_items = [
  ['Чи є на цій локації Cadillac і Reformer?', 'Так, на всіх трьох локаціях студії однаковий набір обладнання: Cadillac, Reformer, Wall Unit і кульки BALLance® для «Здорової спини».'],
  ['Як дізнатись вільний час саме тут?', 'Відкрийте розклад і оберіть цю локацію у фільтрі — побачите тільки заняття на ' . mb_strtolower($loc['label']) . '.'],
  ['Чи можна перейти на іншу локацію', 'Так, абонемент діє на всіх трьох локаціях студії — записуйтесь, де зручно в конкретний день.'],
];
$faq_title = 'Питання про локацію';
$faq_group = 'location-faq';
include 'partials/faq.php';
?>

<?php
$seo_title = 'Студія «Пілатес Львів» на ' . $loc['label'];
$seo_text  = <<<HTML
  <p>
    Зала студії «Пілатес Львів» на {$loc['label']} — {$loc['address']}. Тут
    доступні всі формати занять: пілатес на матах і реформерах, «Здорова
    спина» з кульками BALLance®, персональні та спліт-заняття. Абонемент діє
    на будь-якій з трьох локацій студії.
  </p>
HTML;

include 'partials/footer.php';
?>
