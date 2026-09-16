<?php
/**
 * Записатись — умброве поле («двері»). Одне на сторінку: умбра ніде більше
 * не використовується (CLAUDE.md). Перед include задай:
 *   $cta_title     — заголовок. Обовʼязково.
 *   $cta_text      — лід-абзац під заголовком. Опційно.
 *   $cta_modal     — ключ модалки для кнопки; можна з квері:
 *                    'booking?direction=pilates'. Дефолт 'booking'.
 *   $cta_btn       — підпис основної кнопки. Дефолт 'Записатись'.
 *   $cta_link      — друга кнопка: [підпис, href]. Дефолт — розклад занять;
 *                    на сторінках, де запис не про заняття (співпраця),
 *                    підміняється своєю дією.
 *   $cta_img       — кадр відносно assets/img/. Дефолт 'cta.jpg'.
 *   $cta_img_alt   — alt кадру.
 *   $cta_locations — [[назва, адреса], …] списком під кнопками (як на
 *                    головній). Опційно: team/trainer його не показують.
 *
 * Змінні скидаються в кінці — щоб другий виклик не успадкував налаштування.
 */
$cta_title     = $cta_title     ?? '';
$cta_text      = $cta_text      ?? '';
$cta_modal     = $cta_modal     ?? 'booking';
$cta_btn       = $cta_btn       ?? 'Записатись';
$cta_link      = $cta_link      ?? ['Розклад занять', 'schedule.php'];
$cta_img       = $cta_img       ?? 'cta.jpg';
$cta_img_alt   = $cta_img_alt   ?? 'Групове заняття в залі студії';
$cta_locations = $cta_locations ?? null;
?>
<!-- .patterned на секції, а не на гріді: грід тут лежить у .container,
     і фактура обривалась би по краях картки замість усього умброво поля -->
<section class="split split--umber split--cta split--boxed patterned on-dark">
  <div class="container">
    <div class="split__grid">
      <figure class="split__media">
        <img class="split__layer" src="assets/img/<?= $cta_img ?>" alt="<?= htmlspecialchars($cta_img_alt) ?>"
             width="2048" height="1365" loading="lazy"
             data-anim="parallax" data-parallax="10">
      </figure>

      <div class="split__body" data-anim="parallax" data-parallax="4">
        <h2 data-reveal="lines"><?= $cta_title ?></h2>
        <?php if ($cta_text): ?>
          <p class="text--lead text--muted" data-reveal><?= $cta_text ?></p>
        <?php endif; ?>

        <div class="split__actions" data-reveal>
          <button type="button" class="btn btn--filled btn--light" data-modal="<?= $cta_modal ?>"><?= $cta_btn ?></button>
          <a class="btn btn--outlined btn--light" href="<?= $cta_link[1] ?>"><?= $cta_link[0] ?></a>
        </div>

        <?php if ($cta_locations): ?>
          <?php /* Опис залу сюди не тягнемо: поруч із кнопкою запису потрібна
                   адреса, куди прийти, а не характеристика обладнання. */ ?>
          <ul class="split__locations" data-reveal style="--reveal-i: 1">
            <?php foreach ($cta_locations as $l): ?>
              <li>
                <a href="locations.php">
                  <span class="split__loc-name"><?= $l[0] ?></span>
                  <span class="text--sm text--muted">Львів, <?= $l[1] ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php
unset($cta_title, $cta_text, $cta_modal, $cta_btn, $cta_link, $cta_img, $cta_img_alt, $cta_locations);
