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
// Хто веде тут — форма як у $team на головній: [імʼя, напрямок, цитата, slug].
// TODO: реальний розподіл тренерів по залах від клієнта; поки склад
// роздано умовно, щоб слайдер не показував на трьох локаціях одне й те саме.
$loc_team = [
  'chuprynky' => [
    ['Галина',   'Пілатес на тренажерах', 'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', 'halyna'],
    ['Оксана',   'Пілатес на тренажерах', 'Я допомагаю людям повернути здоров’я та радість життя', 'oksana'],
    ['Сюзанна',  'Пілатес на тренажерах', 'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', 'suzanna'],
    ['Вікторія', 'Йога',                  'Йога — це не спорт, а скоріше мистецтво', 'viktoria-y'],
  ],
  'bryukhovychi' => [
    ['Ірина',    'Пілатес на тренажерах',  'Повернути людині красиву поставу та здорове тіло — це те саме, що повернути віру в себе', 'iryna'],
    ['Оксана',   'Пілатес на тренажерах',  'Я допомагаю людям повернути здоров’я та радість життя', 'oksana'],
    ['Вікторія', 'Функціональний тренінг', 'Вже 9 років з його допомогою я тримаю себе у чудовій формі', 'viktoria-f'],
  ],
  'sykhiv' => [
    ['Сюзанна',  'Пілатес на тренажерах', 'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', 'suzanna'],
    ['Галина',   'Пілатес на тренажерах', 'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', 'halyna'],
    ['Марʼяна', 'Танці',                 'Танець — це спосіб досягнути краси і гармонії, володіючи кожним мʼязом', 'mariana'],
  ],
];
$loc_slug = $_GET['loc'] ?? '';
if (!isset($loc_photos[$loc_slug])) $loc_slug = 'chuprynky';   // невідомий слаг у URL — фолбек на першу локацію

$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим

include 'partials/header.php';   // $contact вже несе header.php — тягнемо тут, дані локації нижче

$loc    = current(array_filter($contact['locations'], fn($l) => $l['slug'] === $loc_slug));
$hall   = $loc['hall'];
$photos = $loc_photos[$loc_slug];

$page_title = $loc['label'] . ' — локації студії «Пілатес Львів»';
$page_description = 'Пілатес-студія на ' . $loc['label'] . ': ' . $loc['address'] . '. Розклад, тренери, запис на тренування.';
?>

<!-- ============================================================
     01 · Банер — фото зали цієї локації, на весь екран (як герой
     головної й about.php): локація — вітрина зали, тож кадр тут не
     смужка над контентом, а сам контент.
     ============================================================ -->
<section class="hero on-dark">
  <img class="hero__video" src="assets/img/location-1/<?= $photos[0] ?>.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <!-- порожній перший елемент, як у героя головної й about: відтискає
           крихти на середину екрана (три позиції space-between) -->
      <div></div>

      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <a href="locations.php">Локації</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page"><?= htmlspecialchars($loc['label']) ?></span>
      </nav>

      <!-- Контакти й кнопки — колонкою під заголовком (базовий .hero__bottom,
           без сітки .hero--intro): адреса з телефоном і є те, по що сюди
           прийшли, тож окрема секція «Загальна інформація» під банером
           більше не потрібна. -->
      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines"><?= mb_strtoupper($loc['label']) ?></h1>

        <address class="hero__contacts" data-reveal style="--reveal-i: 1">
          <a class="icon-link" href="<?= $contact['map'] ?>" target="_blank" rel="noopener">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
            <?= htmlspecialchars($loc['address']) ?>
          </a>
          <a class="icon-link" href="<?= $loc['phone_href'] ?>">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
            <?= htmlspecialchars($loc['phone']) ?>
          </a>
        </address>

        <div class="hero__actions" data-reveal style="--reveal-i: 2">
          <a class="btn btn--filled" href="schedule.php?location=<?= $hall ?>">Розклад на цій локації</a>
          <button type="button" class="btn btn--outlined" data-modal="callback">Замовити дзвінок</button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Тренери цієї локації — той самий слайдер, що на головній
     ============================================================ -->
<?php
$team_items = $loc_team[$loc_slug];
$team_title = 'Тренери на ' . $loc['label'];
$team_lead  = 'Абонемент діє на всіх трьох локаціях — до будь-кого з них можна прийти й в іншу залу.';
$vendor_swiper = true;
include 'partials/team-slider.php';
?>

<!-- ============================================================
     03 · Галерея зали — той самий свайпер, що на головній/тренері
     ============================================================ -->
<?php
$gallery_title = 'Зала на ' . $loc['label'];
$gallery_items = array_map(fn($p) => ["location-1/$p.jpg", 'Зала студії, ' . $loc['label']], $photos);
// TODO: окремий тур на кожну локацію — поки в усіх трьох один, той самий, що на about.php
$gallery_link  = ['3D-тур залою', 'https://app.lapentor.com/sphere/pilates-2'];
$gallery_class = 'pt-0';   // одразу під слайдером тренерів — подвійний відступ зайвий
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
  ['Чи є на цій локації Cadillac і Reformer?', 'Так, на всіх трьох локаціях студії однаковий набір обладнання: Cadillac, Reformer, Wall Unit і Wunda Chair.'],
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
    доступні всі формати занять: Кадилак, реформер, Wunda Chair і Barrel,
    персональні та спліт-заняття. Абонемент діє
    на будь-якій з трьох локацій студії.
  </p>
HTML;

include 'partials/footer.php';
?>
