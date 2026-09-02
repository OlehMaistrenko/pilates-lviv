<?php
/**
 * «Замовити дзвінок» — коротка форма з футера. Тригер — [data-modal="callback"].
 * Та сама логіка, що й у діючого сайту: одне поле, одна дія.
 * Бекенду ще немає — action порожній.
 */
?>
<div class="modal__head">
  <h2 class="modal__title" id="modal-overlay-title">Замовити дзвінок</h2>
  <p class="text text--sm text--muted mt-3">
    Ми передзвонимо та відповімо на питання.
  </p>
</div>

<form class="form modal__body" action="" method="post">
  <div class="form__row">
    <label class="form__label" for="callback-phone">Вкажіть, будь ласка, ваш телефон</label>
    <input class="form__input" type="tel" id="callback-phone" name="phone" autocomplete="tel"
           placeholder="+38 (0__) ___-__-__" required>
  </div>

  <div class="modal__foot">
    <button type="submit" class="btn btn--umber">Передзвоніть мені</button>
  </div>
</form>
