<?php
/**
 * Маркетинговий попап-оверлей — автопоказ за таймером (js/main.js), не
 * через [data-modal]. Контент редагується прямо тут, під конкретну кампанію.
 */
$image    = 'assets/img/cta.jpg'; // TODO: замінити на фото акції
$title    = 'Знижка 20% на перший абонемент'; // TODO: приклад акції, узгодити з клієнтом перед запуском
$text     = 'Для нових клієнтів — знижка на будь-який абонемент у перший місяць занять на Reformer чи Cadillac.';
$ctaLabel = 'Дізнатися умови';
$ctaHref  = 'prices.php';
?>
<div class="promo-popup__media">
  <img src="<?= htmlspecialchars($image) ?>" alt="" loading="eager">
</div>
<div class="promo-popup__body">
  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title"><?= htmlspecialchars($title) ?></h2>
    <p class="text text--muted mt-3"><?= htmlspecialchars($text) ?></p>
  </div>
  <div class="modal__foot">
    <a href="<?= htmlspecialchars($ctaHref) ?>" class="btn btn--filled"><?= htmlspecialchars($ctaLabel) ?></a>
  </div>
</div>
