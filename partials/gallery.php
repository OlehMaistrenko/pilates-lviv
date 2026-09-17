<?php
/**
 * Галерея — coverflow-слайдер із кадрами зали. Один блок на всі сторінки
 * (головна, тренер, стаття). Перед include задай:
 *   $gallery_items  — [[файл відносно assets/img/, alt], …]. Обовʼязково.
 *   $gallery_title  — заголовок секції; '' — секція без шапки.
 *   $gallery_link   — ['текст', 'href'] праворуч у шапці; напр. 3D-тур. Опційно.
 *   $gallery_class  — додатковий клас на <section> (напр. 'pt-0'). Опційно.
 *
 * Потребує $vendor_swiper = true на сторінці (до include footer.php).
 * Змінні скидаються в кінці — щоб два виклики на одній сторінці не
 * успадковували налаштування один одного.
 */
$gallery_items = $gallery_items ?? [];
$gallery_title = $gallery_title ?? 'Зали та тренажери';
$gallery_link  = $gallery_link  ?? null;
$gallery_class = $gallery_class ?? '';

if ($gallery_items):
  // Coverflow зациклений (loop) — Swiper вимагає більше слайдів, ніж
  // видно за раз, інакше на десктопі ряд рветься. Дублюємо набір, доки
  // його не вистачить на найширшу розкладку (4 слайди).
  $slides = $gallery_items;
  while (count($slides) < 8) $slides = array_merge($slides, $gallery_items);
  $unique = count($gallery_items);
?>
<section class="section gallery<?= $gallery_class ? ' ' . $gallery_class : '' ?>">
  <?php if ($gallery_title): ?>
    <div class="container">
      <div class="section-head section-head--split">
        <h2 data-reveal="lines"><?= $gallery_title ?></h2>
        <?php if ($gallery_link): ?>
          <a class="btn btn--outlined" href="<?= $gallery_link[1] ?>" target="_blank" rel="noopener" data-reveal>
            <?= $gallery_link[0] ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="swiper-wrap gallery__slider" data-reveal>
    <div class="swiper" data-swiper='{"effect":"coverflow","grabCursor":true,"centeredSlides":true,"slidesPerView":1.3,"loop":true,"autoplay":{"delay":2800,"disableOnInteraction":false},"coverflowEffect":{"rotate":35,"stretch":0,"depth":220,"modifier":1,"slideShadows":false},"pagination":{"dynamicBullets":true},"breakpoints":{"768":{"slidesPerView":2},"1080":{"slidesPerView":4}}}'>
      <div class="swiper-wrapper">
        <?php foreach ($slides as $i => [$file, $alt]): ?>
          <?php /* Слайди після першого проходу — дублікати заради loop.
                   У лайтбокс потрапляє лише перший прохід (data-glightbox),
                   інакше той самий кадр ішов би в галереї по кілька разів;
                   дублікат відкриває оригінал за спільним data-gl-index.
                   data-gallery ізолює набір: інакше стрілки в лайтбоксі
                   вели б із залів у сертифікати чи відеовідгук. */ ?>
          <figure class="swiper-slide gallery__item">
            <a class="gallery__zoom" href="assets/img/<?= $file ?>"
               data-gl-index="<?= $i % $unique ?>"
               <?= $i < $unique ? 'data-glightbox data-gallery="gallery"' : '' ?>
               aria-label="Відкрити фото на весь екран">
              <img src="assets/img/<?= $file ?>" alt="<?= htmlspecialchars($alt) ?>" loading="lazy">
            </a>
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="slider-controls mt-5">
      <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попереднє фото">
        <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
      </button>
      <div class="swiper-pagination"></div>
      <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступне фото">
        <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
      </button>
    </div>
  </div>
</section>
<?php endif;

unset($gallery_items, $gallery_title, $gallery_link, $gallery_class, $slides, $unique);
