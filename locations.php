<?php
$nav = 'locations';
$page_title = 'Локації — студія «Пілатес Львів»';
$page_description = 'Три локації студії «Пілатес Львів»: Чупринки, Брюховичі, Сихів. Адреси, розклад і запис на тренування.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий .hero--page, що на team/schedule/blog
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/gallery/3.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Локації</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">ЛОКАЦІЇ</h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Три зали у Львові. Визначте, де ви, — покажемо найближчий і маршрут
          до нього пішки чи автом.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Карта — зали, «де я», маршрут (partials/map.php; $vendor_map
     виставляє сам партіал, коли є токен)
     ============================================================ -->
<?php include 'partials/map.php'; ?>

<!-- ============================================================
     03 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Оберіть зручну локацію';
$cta_text  = 'Розклад і вільні місця різні на кожній локації — подзвоніть, підкажемо найближчу вільну годину.';
include 'partials/cta.php';
?>

<?php
$seo_title = 'Локації студії «Пілатес Львів» у Львові';
$seo_text  = <<<HTML
  <p>
    Студія працює на трьох локаціях: Чупринки, Брюховичі та Сихів. На кожній —
    власний розклад, тренери й обладнання для пілатесу на Cadillac і Reformer.
    Оберіть найближчу до дому чи роботи локацію та дивіться розклад занять.
  </p>
HTML;

include 'partials/footer.php';
?>
