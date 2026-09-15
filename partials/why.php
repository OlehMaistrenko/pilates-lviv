<?php
/**
 * Банер із цифрами — фонове фото з паралаксом, ряд цифр, аргументи знизу
 * (головна, про студію). Перед include задай:
 *   $why_title — заголовок; <em> фарбує слово золотом. Обовʼязково.
 *   $why_facts — [[цифра, підпис], …].
 *   $why_items — [[заголовок, текст], …]; порожній — без нижнього ряду.
 *   $why_img   — кадр відносно assets/img/. Дефолт 'gallery/1.jpg'.
 *
 * Змінні скидаються в кінці.
 */
$why_title = $why_title ?? '';
$why_facts = $why_facts ?? [];
$why_items = $why_items ?? [];
$why_img   = $why_img   ?? 'gallery/1.jpg';
?>
<section class="why on-dark">
  <div class="why__bg" aria-hidden="true">
    <img class="why__layer" src="assets/img/<?= $why_img ?>" alt=""
         width="2048" height="1365" loading="lazy"
         data-anim="parallax" data-parallax="12">
  </div>

  <div class="container why__inner">
    <h2 class="why__title" data-reveal="lines"><?= $why_title ?></h2>

    <?php if ($why_facts): ?>
      <dl class="why__facts">
        <?php foreach ($why_facts as $i => [$num, $caption]): ?>
          <div data-reveal style="--reveal-i: <?= $i ?>"><dt><?= $num ?></dt><dd><?= $caption ?></dd></div>
        <?php endforeach; ?>
      </dl>
    <?php endif; ?>

    <?php if ($why_items): ?>
      <div class="why__list">
        <?php foreach ($why_items as $i => [$title, $text]): ?>
          <div class="why__item" data-reveal style="--reveal-i: <?= $i ?>">
            <h3><?= $title ?></h3>
            <p><?= $text ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php unset($why_title, $why_facts, $why_items, $why_img); ?>
