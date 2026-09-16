<?php
/**
 * «Замовити дзвінок» — коротка форма з футера. Тригер — [data-modal="callback"].
 * Та сама логіка, що й у діючого сайту: одне поле, одна дія.
 * Зі сторінки цін тригер несе [data-modal="callback?tariff=…"], з
 * навчального центру — [data-modal="callback?course=…"]; назва тоді йде
 * в заголовок і в приховане поле форми.
 * Бекенду ще немає — action порожній.
 */
$tariff = $_GET['tariff'] ?? '';
$course = $_GET['course'] ?? '';
?>
<div class="modal__head">
  <h2 class="modal__title" id="modal-overlay-title"><?= $course ? 'Записатись на курс' : ($tariff ? 'Купити абонемент' : 'Замовити дзвінок') ?></h2>
  <p class="text text--muted mt-3">
    <?php if ($course): ?>
      Курс: «<?= htmlspecialchars($course) ?>». Залиште телефон — передзвонимо, спитаємо про ваш досвід і розкажемо про програму.
    <?php elseif ($tariff): ?>
      Тариф: «<?= htmlspecialchars($tariff) ?>». Залиште телефон — передзвонимо й оформимо.
    <?php else: ?>
      Ми передзвонимо та відповімо на питання.
    <?php endif; ?>
  </p>
</div>

<form class="form modal__body" action="" method="post">
  <?php if ($tariff): ?>
    <input type="hidden" name="tariff" value="<?= htmlspecialchars($tariff) ?>">
  <?php endif; ?>
  <?php if ($course): ?>
    <input type="hidden" name="course" value="<?= htmlspecialchars($course) ?>">
  <?php endif; ?>

  <div class="form__row">
    <label class="form__label" for="callback-phone">Вкажіть, будь ласка, ваш телефон</label>
    <input class="form__input" type="tel" id="callback-phone" name="phone" autocomplete="tel"
           placeholder="+380 63 015 05 17" required>
  </div>

  <div class="modal__foot">
    <button type="submit" class="btn btn--filled">Передзвоніть мені</button>
  </div>
</form>
