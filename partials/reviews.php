<?php
/**
 * Відгуки — мохове поле зі слайдером цитат. Перед include задай:
 *   $reviews_items — [[текст, імʼя, напрямок, файл кадру в assets/img/gallery/,
 *                    ?файл відео в assets/video/], …]. Обовʼязково.
 *                    Пʼятий елемент необовʼязковий: якщо є — кадр стає
 *                    відеовідгуком (play + GLightbox), тоді сторінці
 *                    потрібен ще $vendor_lightbox = true.
 *   $reviews_title — заголовок секції.
 *
 * Мохове поле: рахуй разом із героєм і футером — їх на сторінці максимум три
 * (CLAUDE.md). Потребує $vendor_swiper = true на сторінці.
 * Змінні скидаються в кінці.
 */
$reviews_items = $reviews_items ?? [];
$reviews_title = $reviews_title ?? 'Що кажуть клієнти';

if ($reviews_items):
?>
<section class="reviews patterned on-dark">
  <div class="container">
    <!-- .swiper-wrap огортає всю сітку: initSwiper шукає контроли в межах
         найближчого .swiper-wrap, а вони стоять у колонці заголовка -->
    <div class="swiper-wrap reviews__grid" data-reveal>
      <div class="reviews__head">
        <h2 data-reveal="lines"><?= $reviews_title ?></h2>
        <div class="slider-controls reviews__controls mt-5">
          <button type="button" class="btn btn--icon btn--outlined btn--light swiper-prev" aria-label="Попередній відгук">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
          </button>
          <div class="swiper-pagination"></div>
          <button type="button" class="btn btn--icon btn--outlined btn--light swiper-next" aria-label="Наступний відгук">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
          </button>
        </div>
      </div>

      <div class="swiper reviews__swiper" data-swiper='{"slidesPerView":1,"autoHeight":true,"spaceBetween":48,"loop":true}'>
        <div class="swiper-wrapper">
          <?php foreach ($reviews_items as $i => $r): [$text, $name, $role, $shot] = $r; $video = $r[4] ?? ''; ?>
            <blockquote class="swiper-slide review">
              <figure class="review__shot">
                <?php if ($video): ?>
                  <?php /* своя data-gallery на слайд: ролик у лайтбоксі сам,
                           без стрілок у галерею залів (їх вендор ховає, коли
                           в групі один елемент). */ ?>
                  <a class="review__play" href="assets/video/<?= $video ?>"
                     data-glightbox data-gallery="review-<?= $i ?>"
                     aria-label="Дивитись відеовідгук — <?= $name ?>">
                    <img src="assets/img/gallery/<?= $shot ?>" alt="" loading="lazy">
                    <span class="btn btn--icon btn--filled video-card__play" aria-hidden="true">
                      <svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-play"></use></svg>
                    </span>
                  </a>
                <?php else: ?>
                  <img src="assets/img/gallery/<?= $shot ?>" alt="" loading="lazy">
                <?php endif; ?>
              </figure>
              <div class="review__body">
                <p class="quote review__text"><?= $text ?></p>
                <footer class="review__meta">
                  <span class="review__name"><?= $name ?></span>
                  <span class="text--sm text--muted"><?= $role ?></span>
                </footer>
              </div>
            </blockquote>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif;

unset($reviews_items, $reviews_title);
