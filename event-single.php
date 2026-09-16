<?php
$nav = 'events';

$all_events = require __DIR__ . '/partials/events-data.php';

$event_slug = $_GET['event'] ?? '';
if (!isset($all_events[$event_slug])) $event_slug = array_key_first($all_events);   // невідомий слаг у URL — фолбек на найближчу подію
$ev = $all_events[$event_slug];

// Інші події — без поточної; на WP це буде запит «наступні за датою»
$others = array_slice(array_diff_key($all_events, [$event_slug => 1]), 0, 3, true);

$page_title = $ev['title'] . ' — події студії «Пілатес Львів»';
$page_description = $ev['date'] . ', ' . mb_strtolower(mb_substr($ev['note'], 0, 1)) . mb_substr($ev['note'], 1);
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий .hero--page, що на blog-single.php:
     дата й назва події стоять просто в кадрі
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/<?= $ev['img'] ?>" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <a href="events.php">Події</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page"><?= $ev['crumb'] ?></span>
      </nav>

      <div class="hero__bottom">
        <div class="text--sm text--muted" data-reveal>
          <time datetime="<?= $ev['dt'] ?>"><?= $ev['date'] ?></time>
          <?php if (!empty($ev['when'])): ?>
            <span aria-hidden="true"> · </span><?= $ev['when'] ?>
          <?php endif; ?>
        </div>

        <h1 class="hero__title hero__title--post" data-reveal="lines" style="--reveal-i: 1"><?= $ev['title'] ?></h1>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Факти й опис — .event__details: факти в тій самій вузькій
     колонці, що дата в рядку списку
     ============================================================ -->
<section class="section section--tight-top">
  <div class="container container--narrow">
    <div class="event__details" data-reveal>
      <aside>
        <ul class="rows">
          <?php foreach ($ev['facts'] as [$label, $value]): ?>
            <li class="rows__item rows__item--tariff">
              <span class="label text--muted"><?= $label ?></span>
              <span class="text--sm"><?= $value ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <button type="button" class="btn btn--filled btn--block mt-6"
                data-modal="callback?event=<?= rawurlencode($ev['title']) ?>">Записатись</button>
      </aside>

      <div class="simple-text"><?= $ev['text'] ?></div>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Залишились питання';
$cta_text  = 'Подзвоніть або залиште телефон — розкажемо про програму, скажемо, скільки лишилось місць, і що взяти з собою.';
$cta_btn   = 'Залишити заявку';
$cta_modal = 'callback?event=' . rawurlencode($ev['title']);
$cta_img   = $ev['img'];
$cta_img_alt = 'Заняття в залі студії';
include 'partials/cta.php';
?>

<!-- ============================================================
     04 · Інші події — той самий список, що на events.php
     ============================================================ -->
<?php
$events_items = $others;
$events_title = 'Інші події';
$events_link  = ['Усі події', 'events.php'];
include 'partials/events.php';
?>

<?php
include 'partials/footer.php';
?>
