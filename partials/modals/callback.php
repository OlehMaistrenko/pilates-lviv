<?php
/**
 * «Замовити дзвінок» — коротка форма з футера. Тригер — [data-modal="callback"].
 * Та сама логіка, що й у діючого сайту: одне поле, одна дія.
 * Тригер може нести назву того, на що записуються — вона йде в заголовок
 * і в приховане поле форми:
 *   ?tariff=… — абонемент зі сторінки цін
 *   ?course=… — курс навчального центру (весь центр або окремий тариф)
 *   ?event=…  — подія чи потік зі списку (partials/events.php)
 * Параметри різні, бо в поле форми мусить лягти те, чим воно є: воркшоп
 * на дві години — не курс.
 * Бекенду ще немає — action порожній.
 */
$tariff = $_GET['tariff'] ?? '';
$course = $_GET['course'] ?? '';
$event  = $_GET['event']  ?? '';
?>
<div class="modal__head">
  <h2 class="modal__title" id="modal-overlay-title">
    <?php if ($course): ?>Записатись на курс
    <?php elseif ($event): ?>Записатись
    <?php elseif ($tariff): ?>Купити абонемент
    <?php else: ?>Замовити дзвінок<?php endif; ?>
  </h2>
  <p class="text text--muted mt-3">
    <?php if ($course): ?>
      Курс: «<?= htmlspecialchars($course) ?>». Залиште телефон — передзвонимо, спитаємо про ваш досвід і розкажемо про програму.
    <?php elseif ($event): ?>
      «<?= htmlspecialchars($event) ?>». Залиште телефон — передзвонимо, підтвердимо місце й скажемо, що взяти з собою.
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
  <?php if ($event): ?>
    <input type="hidden" name="event" value="<?= htmlspecialchars($event) ?>">
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
