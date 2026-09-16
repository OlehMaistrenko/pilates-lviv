<?php
/**
 * Запит на співпрацю. Тригер — [data-modal="partnership"].
 * З карток пропозицій тригер несе формат: data-modal="partnership?format=rent"
 * — той самий приймач, що $direction у booking.php.
 * Бекенду ще немає — action порожній.
 */
$format = $_GET['format'] ?? '';

// Ключі збігаються з $offers у partnership.php
$formats = [
  'private'   => 'Приватне заняття для команди',
  'office'    => 'Пілатес у вашому офісі',
  'corporate' => 'Знижки для співробітників',
  'rent'      => 'Оренда залу тренерам',
  'medical'   => 'Направлення пацієнтів',
  'brand'     => 'Зйомка, подія, колаборація',
];
?>
<div class="modal__head">
  <h2 class="modal__title" id="modal-overlay-title">Запит на співпрацю</h2>
  <p class="text text--muted mt-3">
    Відповімо в робочий час: скажемо, чи можемо взяти ваш формат, і назвемо ціну.
  </p>
</div>

<form class="form modal__body" action="" method="post">
  <div class="form__row">
    <label class="form__label" for="partnership-name">Ім’я</label>
    <input class="form__input" type="text" id="partnership-name" name="name" autocomplete="name" required>
  </div>

  <div class="form__row">
    <label class="form__label" for="partnership-company">Компанія або напрям діяльності</label>
    <input class="form__input" type="text" id="partnership-company" name="company" autocomplete="organization">
  </div>

  <div class="form__row">
    <label class="form__label" for="partnership-phone">Телефон</label>
    <input class="form__input" type="tel" id="partnership-phone" name="phone" autocomplete="tel"
           placeholder="+380 63 015 05 17" required>
  </div>

  <div class="form__row">
    <label class="form__label" for="partnership-format">Формат</label>
    <select class="form__input" id="partnership-format" name="format" data-slimselect>
      <option value="" data-placeholder="true">Ще не обрав</option>
      <?php foreach ($formats as $key => $label): ?>
        <option value="<?= $key ?>"<?= $key === $format ? ' selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form__row">
    <label class="form__label" for="partnership-note">Коротко про запит</label>
    <textarea class="form__textarea" id="partnership-note" name="note" rows="4"
              placeholder="Скільки людей, як часто, у які дні"></textarea>
  </div>

  <div class="modal__foot">
    <button type="submit" class="btn btn--filled">Надіслати запит</button>
    <a class="btn btn--outlined" href="tel:+380630150517">Подзвонити</a>
  </div>
</form>
