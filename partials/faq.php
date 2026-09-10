<?php
/**
 * FAQ — акордеон із телефоном у колонці заголовка. Перед include задай:
 *   $faq_items — [[питання, відповідь], …]. Обовʼязково.
 *   $faq_title — заголовок секції.
 *   $faq_lead  — рядок над телефоном.
 *   $faq_group — ключ data-accordion-group і префікс id для aria-controls.
 *                Дефолт 'faq'. Два FAQ на одній сторінці мусять мати різні
 *                ключі — інакше збігнуться id і зламається ексклюзивність.
 *
 * $contact приходить із header.php, тож include лише після нього.
 * Змінні скидаються в кінці.
 */
$faq_items = $faq_items ?? [];
$faq_title = $faq_title ?? 'Питання перед першим заняттям';
$faq_lead  = $faq_lead  ?? 'Не знайшли своє — подзвоніть:';
$faq_group = $faq_group ?? 'faq';

if ($faq_items):
?>
<section class="section faq">
  <div class="container">
    <div class="faq__grid">
      <div class="faq__head">
        <h2 data-reveal="lines"><?= $faq_title ?></h2>
        <p class="text--muted" data-reveal><?= $faq_lead ?></p>
        <div data-reveal>
          <a class="icon-link faq__phone" href="<?= $contact['phone_href'] ?>" >
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
            <?= $contact['phone'] ?>
          </a>
        </div>
      </div>

      <div class="faq__list" data-reveal style="--reveal-i: 1">
        <?php foreach ($faq_items as $i => [$q, $a]): ?>
          <div class="accordion" data-accordion-group="<?= $faq_group ?>">
            <div class="accordion__summary" role="button" tabindex="0" aria-expanded="false" aria-controls="<?= $faq_group ?>-<?= $i ?>">
              <h3 class="accordion__title"><?= $q ?></h3>
              <span class="accordion__icon" aria-hidden="true"></span>
            </div>
            <div class="accordion__body" id="<?= $faq_group ?>-<?= $i ?>">
              <div class="accordion__body-inner">
                <p class="text--muted faq__answer"><?= $a ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif;

unset($faq_items, $faq_title, $faq_lead, $faq_group);
