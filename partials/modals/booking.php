<?php
/**
 * Запис на заняття. Тригер — [data-modal="booking"].
 * Можна передати напрямок: data-modal="booking?direction=pilates"
 * Бекенду ще немає — action порожній, форма поки не сабмітиться нікуди.
 */
$direction = $_GET['direction'] ?? '';

$directions = [
  'pilates'  => 'Пілатес Springtone',
  'recovery' => 'Функціональне відновлення',
  'yoga'     => 'Йога',
  'dance'    => 'Танці',
  'physio'   => 'Консультація фізіолога',
  'academy'  => 'Навчальний центр',
];
?>
<div class="modal__head">
  <h2 class="modal__title" id="modal-overlay-title">Записатись на заняття</h2>
  <p class="text text--muted mt-3">
    Передзвонимо, підберемо час і відповімо на питання. Перше заняття — знайомство.
  </p>
</div>

<form class="form modal__body" action="" method="post">
  <div class="form__row">
    <label class="form__label" for="booking-name">Ім’я</label>
    <input class="form__input" type="text" id="booking-name" name="name" autocomplete="name" required>
  </div>

  <div class="form__row">
    <label class="form__label" for="booking-phone">Телефон</label>
    <input class="form__input" type="tel" id="booking-phone" name="phone" autocomplete="tel"
           placeholder="+380 63 015 05 17" required>
  </div>

  <div class="form__row">
    <label class="form__label" for="booking-direction">Напрямок</label>
    <select class="form__input" id="booking-direction" name="direction" data-slimselect>
      <option value="" data-placeholder="true">Ще не обрав</option>
      <?php foreach ($directions as $key => $label): ?>
        <option value="<?= $key ?>"<?= $key === $direction ? ' selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="modal__foot">
    <button type="submit" class="btn btn--filled">Записатись</button>
    <a class="btn btn--outlined" href="tel:+380630150517">Подзвонити</a>
  </div>
</form>
