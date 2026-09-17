<?php
// Один шаблон на весь каталог тренувань (той самий принцип, що
// location-single.php ?loc= і event-single.php ?event=) — раніше кожен
// напрямок був окремим файлом (training-reformer.php тощо), контент
// звідти переїхав у partials/trainings-data.php.
$all_trainings = require __DIR__ . '/partials/trainings-data.php';

$training_slug = $_GET['training'] ?? '';
if (!isset($all_trainings[$training_slug])) $training_slug = array_key_first($all_trainings);   // невідомий слаг у URL — фолбек на перший
$t = $all_trainings[$training_slug];

$nav = 'trainings';   // той самий принцип, що location-single.php: підсвічує лише пункт-батько
$page_title = $t['title'] . ' у Львові — студія «Пілатес Львів»';
$page_description = $t['lead'];
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // переваги, тренери, галерея, відгуки
$vendor_lightbox = true;    // клік по кадру галереї

// Хто веде — той самий склад «Пілатес на тренажерах», що на about.php:
// усі шість напрямків каталогу ведуть ці чотири тренерки.
$team = [
  ['Галина',  'Пілатес на тренажерах', 'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', 'halyna'],
  ['Оксана',  'Пілатес на тренажерах', 'Я допомагаю людям повернути здоров’я та радість життя', 'oksana'],
  ['Сюзанна', 'Пілатес на тренажерах', 'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', 'suzanna'],
  ['Ірина',   'Пілатес на тренажерах', 'Повернути людині красиву поставу та здорове тіло — це те саме, що повернути віру в себе', 'iryna'],
];

// Відгуки — ті самі два, що на trainings.php, роль підписана під поточний
// напрямок: тексти не про конкретний тренажер, а про результат.
$reviews = [
  ['Після другої дитини боліла поперек — на реформері за два місяці пройшло. Тренер щоразу дивиться, як саме я роблю вправу, а не просто рахує повтори.', 'Марта', mb_strtolower($t['title']), '6.jpg'],
  ['Пробувала пілатес у трьох студіях. Тут єдині, де є Cadillac, і єдині, де мені пояснили, навіщо кожна вправа.', 'Оля', mb_strtolower($t['title']), '4.jpg'],
];

// Відео-презентація. Окремого ролика на кожен напрямок клієнт ще не дав,
// тому поки один спільний; 'video' у partials/trainings-data.php перебиває.
$video_id = $t['video'] ?? 'MvjMk6BaMtc';

// Інші тренування каталогу — без поточного, до трьох
$others = array_slice(array_diff_key($all_trainings, [$training_slug => 1]), 0, 3, true);

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/<?= $t['banner'] ?>" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <a href="trainings.php">Тренування</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page"><?= $t['crumb'] ?></span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines"><?= $t['hero_title'] ?></h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          <?= $t['lead'] ?>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Переваги — свайпер карток
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2 data-reveal="lines">Переваги</h2>
    </div>

    <div class="swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":1.2,"spaceBetween":24,"breakpoints":{"769":{"slidesPerView":2.2},"1081":{"slidesPerView":4}}}'>
        <ul class="swiper-wrapper">
          <?php foreach ($t['benefits'] as [$img, $title, $text]): ?>
            <li class="benefit-card swiper-slide">
              <span class="benefit-card__media">
                <img src="assets/img/<?= $img ?>" alt="" loading="lazy">
              </span>
              <span class="benefit-card__title"><?= $title ?></span>
              <p class="text--sm text--muted"><?= $text ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередня перевага">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступна перевага">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Загальна інформація — .simple-text, той самий набір тегів,
     що в blog-single.php
     ============================================================ -->
<section class="section pt-0">
  <div class="container container--narrow">
    <div class="section-head section-head--center">
      <h2 data-reveal="lines"><?= $t['info_title'] ?></h2>
    </div>

    <div class="simple-text" data-reveal>
      <?= $t['info_html'] ?>
    </div>
  </div>
</section>

<!-- ============================================================
     03b · Найближчі події — лише для 'events' (partials/events.php,
     той самий блок, що на events.php): три клієнтські воркшопи
     найближче за датою + кнопка на повний список.
     ============================================================ -->
<?php if (!empty($t['show_upcoming_events'])):
  $all_events = require __DIR__ . '/partials/events-data.php';
  $upcoming = array_filter($all_events, fn($e) => $e['type'] === 'client');
  uasort($upcoming, fn($a, $b) => $a['dt'] <=> $b['dt']);

  $events_items = array_slice($upcoming, 0, 3, true);
  $events_title = 'Найближчі події';
  $events_link  = ['Усі події', 'events.php'];
  $events_class = 'pt-0';
  include 'partials/events.php';
endif; ?>

<!-- ============================================================
     04 · Хто веде
     ============================================================ -->
<?php
$team_items = $team;
$team_title = 'Хто веде';
$team_all   = false;
$team_class = 'pt-0';
include 'partials/team-slider.php';
?>

<!-- ============================================================
     05 · Галерея — спільний блок (partials/gallery.php)
     ============================================================ -->
<?php
$gallery_items = $t['gallery'];
$gallery_title = 'Як це виглядає';
$gallery_class = 'pt-0';
include 'partials/gallery.php';
?>

<!-- ============================================================
     06 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = $t['cta_title'];
$cta_text  = $t['cta_text'];
$cta_modal = $t['cta_modal'] ?? 'booking?direction=pilates';
$cta_btn   = $t['cta_btn'] ?? 'Записатись';
include 'partials/cta.php';
?>

<!-- ============================================================
     07 · Відгуки — друге мохове поле (третє — футер)
     ============================================================ -->
<?php
$reviews_items = $reviews;
include 'partials/reviews.php';
?>

<!-- ============================================================
     07b · Відео-презентація — голий <iframe> у .simple-text, як на
     academy.php: aspect-ratio стоїть на самому iframe, обгортка не потрібна
     ============================================================ -->
<section class="section">
  <div class="container container--narrow">
    <div class="section-head section-head--center">
      <h2 data-reveal="lines">Як проходить заняття</h2>
    </div>

    <div class="simple-text" data-reveal>
      <figure>
        <iframe src="https://www.youtube.com/embed/<?= $video_id ?>"
                title="<?= htmlspecialchars($t['title']) ?> у студії «Пілатес Львів»"
                loading="lazy" allowfullscreen></iframe>
        <figcaption>Ціле заняття за півтори хвилини: як тренер ставить рух і що робить група</figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- ============================================================
     08 · FAQ
     ============================================================ -->
<?php
$faq_items = $t['faq'];
$faq_title = 'Питання про ' . mb_strtolower($t['title']);
$faq_group = 'training-detail-faq';
include 'partials/faq.php';
?>

<!-- ============================================================
     09 · Інші тренування — генерична сітка карток (.posts)
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Інші тренування</h2>
      <a class="btn btn--outlined" href="trainings.php">Усі тренування</a>
    </div>

    <ul class="posts">
      <?php $i = 0; foreach ($others as $slug => $o): ?>
        <li class="post" data-reveal style="--reveal-i: <?= $i ?>">
          <a class="post__link" href="training-detail.php?training=<?= $slug ?>">
            <!-- alt порожній навмисно: назва вже є текстом самого посилання -->
            <span class="post__media">
              <img src="assets/img/directions/<?= $slug ?>.jpg" alt="" width="2048" height="1365" loading="lazy">
            </span>
            <span class="post__title"><?= $o['title'] ?></span>
            <span class="card__note text--muted"><?= $o['lead'] ?></span>
          </a>
        </li>
      <?php $i++; endforeach; ?>
    </ul>
  </div>
</section>

<?php
$seo_title = $t['seo_title'];
$seo_text  = $t['seo_text'];

include 'partials/footer.php';
?>
