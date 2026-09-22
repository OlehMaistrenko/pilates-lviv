<?php
// AJAX-фільтрацію (js/schedule.js) обробляє не ця сторінка, а зв'язка
// partials/header.php (відкриває буфер) + partials/schedule.php (викидає
// його й друкує лише фрагмент). Власного гейта тут немає навмисно: він
// працював би тільки на цій сторінці, а розклад стоїть ще й на детальній
// тренера.
$nav = 'schedule';
$page_title = 'Розклад занять — студія «Пілатес Львів»';
$page_description = 'Розклад занять студії «Пілатес Львів»: пілатес на Cadillac і Reformer, здорова спина, персональні та спліт-заняття. Три локації — Чупринки, Брюховичі, Сихів.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий .hero--page, що на trainings/team/blog
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/gallery/5.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Розклад</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">РОЗКЛАД</h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Оберіть локацію, напрямок або тренера — покажемо, коли є заняття.
          Отримайте стартову пропозицію на перше заняття.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Календар — день / тиждень / місяць із фільтрами
     ============================================================ -->
<?php
$schedule_filters = true;
$schedule_title = '';   // заголовок дублював би H1 банера
$schedule_class = 'section--tight-top';   // секція одразу під банером
include 'partials/schedule.php';
?>

<!-- ============================================================
     03 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Не знайшли зручний час';
$cta_text  = 'Подзвоніть — підберемо годину під ваш графік. Персональні заняття ставимо поза сіткою розкладу.';
include 'partials/cta.php';
?>

<?php
$seo_title = 'Розклад занять у студії «Пілатес Львів»';
$seo_text  = <<<HTML
  <p>
    Заняття йдуть на трьох локаціях: Чупринки, Брюховичі й Сихів. У сітці —
    групові заняття Кадилак і реформер, Wunda Chair і Barrel,
    спліт на двох-трьох людей. Персональні заняття
    ставляться окремо: час узгоджуємо з тренером під ваш графік.
  </p>
  <p>
    Ми одна з перших у Львові пілатес-студій з професійним обладнанням
    Cadillac і Reformer. Якщо йдете вперше, беріть будь-яке заняття з позначкою «група»
    або зателефонуйте — підкажемо, з чого почати саме вам.
  </p>
HTML;

include 'partials/footer.php';
?>
