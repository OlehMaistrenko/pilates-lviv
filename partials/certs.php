<?php
/**
 * Сертифікати — слайдер сканів, клік відкриває скан на весь екран.
 * Перед include задай:
 *   $certs_items — [[назва, організація, рік, файл відносно assets/img/], …]. Обовʼязково.
 *   $certs_title — заголовок секції. Дефолт «Сертифікати».
 *   $certs_lead  — лід-абзац праворуч у шапці. Опційно.
 *   $certs_group — імʼя групи лайтбокса; окреме, якщо на сторінці два набори.
 *   $certs_class — додатковий клас на <section> (напр. 'pt-0'). Опційно.
 *
 * Потребує $vendor_swiper і $vendor_lightbox = true на сторінці (до include footer.php).
 * Змінні скидаються в кінці — щоб два виклики на одній сторінці не
 * успадковували налаштування один одного.
 */
$certs_items = $certs_items ?? [];
$certs_title = $certs_title ?? 'Сертифікати';
$certs_lead  = $certs_lead  ?? null;
$certs_group = $certs_group ?? 'certs';
$certs_class = $certs_class ?? '';

if ($certs_items):
?>
<section class="section<?= $certs_class ? ' ' . $certs_class : '' ?>">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines"><?= $certs_title ?></h2>
      <?php if ($certs_lead): ?>
        <p class="text--lead text--muted" data-reveal><?= $certs_lead ?></p>
      <?php endif; ?>
    </div>

    <div class="swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":1.2,"spaceBetween":24,"breakpoints":{"769":{"slidesPerView":2.2},"1081":{"slidesPerView":3.2},"1440":{"slidesPerView":5}}}'>
        <ul class="certs swiper-wrapper">
          <?php foreach ($certs_items as [$title, $org, $year, $img]): ?>
            <li class="cert swiper-slide">
              <?php /* без data-title/data-description: назва стоїть під
                       карткою, у лайтбоксі підпис лише перекривав би скан.
                       $org і $year лишаються в alt — скрінрідеру потрібен
                       повний опис документа, якого на екрані вже немає. */ ?>
              <a class="cert__zoom" href="assets/img/<?= $img ?>"
                 data-glightbox data-gallery="<?= $certs_group ?>"
                 aria-label="Відкрити на весь екран: <?= htmlspecialchars($title) ?>">
                <span class="cert__media">
                  <img src="assets/img/<?= $img ?>" alt="<?= htmlspecialchars($title) ?>, <?= htmlspecialchars($org) ?>, <?= $year ?>" loading="lazy">
                </span>
                <span class="cert__title"><?= $title ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередній сертифікат">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступний сертифікат">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>
<?php endif;

unset($certs_items, $certs_title, $certs_lead, $certs_group, $certs_class);
