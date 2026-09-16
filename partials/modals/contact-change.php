<?php
/**
 * Зміна пошти або телефону з профілю кабінету. Тригер —
 * [data-modal="contact-change?field=email|phone"] зі сторінки account.php.
 *
 * Не плутати з ВХОДОМ (partials/modals/auth.php) — авторизація на
 * проєкті лишається за телефоном/SMS (докладно там). Тут — зміна
 * контактних даних уже залогіненого клієнта, це окремі ендпоінти
 * особистих даних (Authorization: Bearer), і флоу в них різні:
 *
 *   POST /user/phone_update/        {phone*}       → SMS з кодом
 *   POST /user/phone_update_verify/ {code*}        → підтверджує номер
 *        → стан ?step=code із полем для коду.
 *
 *   POST /user/email_update/  {email*, next_url*}
 *        → на нову адресу йде лист із посиланням, коду немає — одразу
 *          стан «перевірте пошту». Формат самого посилання й що
 *          повертається на next_url — НЕЗ'ЯСОВАНО (те саме, що в
 *          auth.php було для входу): у схемі немає окремого
 *          email_verify, лист веде або на сторінку InstaSport, або на
 *          наш next_url із власним токеном без задокументованого
 *          прийомного ендпоінта. Email тут — не спосіб входу, тож
 *          некритично: якщо лист виявиться нероздатним, поле пошти
 *          лишається лише довідковим, без дії «Змінити».
 *
 * Обидва потребують Authorization: Bearer (клієнт уже в кабінеті).
 */
$field = ($_GET['field'] ?? 'email') === 'phone' ? 'phone' : 'email';
$step  = $_GET['step'] ?? '';
$is_phone = $field === 'phone';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
// next_url на головну кабінету: сторінки-приймача листа немає (див. докблок
// вище) — до з'ясування формату лист веде туди, звідки почали.
$next_url = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/account.php';
?>
<?php if (!$is_phone && $step === 'sent'): ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Перевірте пошту</h2>
    <p class="text text--muted mt-3">
      Ми надіслали лист на нову адресу. Перейдіть за посиланням у ньому —
      після цього пошта зміниться.
    </p>
  </div>

  <div class="modal__body">
    <div class="modal__foot">
      <button type="button" class="btn btn--outlined" data-modal-close>Зрозуміло</button>
    </div>
  </div>

<?php elseif ($is_phone && $step === 'code'): ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Підтвердіть номер</h2>
    <p class="text text--muted mt-3">
      Ми надіслали код на новий номер. Введіть його — і номер зміниться.
    </p>
  </div>

  <!-- POST /user/phone_update_verify/ {code} -->
  <form class="form modal__body" action="" method="post" data-remote data-user="phone-verify">
    <div class="form__row">
      <label class="form__label" for="contact-code">Код із SMS</label>
      <input class="form__input" type="text" id="contact-code" name="code"
             inputmode="numeric" autocomplete="one-time-code" placeholder="0000" required>
    </div>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">Підтвердити</button>
      <button type="button" class="btn btn--outlined" data-modal="contact-change?field=phone">Змінити номер</button>
    </div>
  </form>

<?php else: ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">
      <?= $is_phone ? 'Змінити телефон' : 'Змінити пошту' ?>
    </h2>
    <p class="text text--muted mt-3">
      <?= $is_phone
        ? 'Введіть новий номер — надішлемо код підтвердження.'
        : 'Введіть нову адресу — надішлемо на неї лист із посиланням.' ?>
    </p>
  </div>

  <form class="form modal__body" action="" method="post" data-remote
        data-user="<?= $is_phone ? 'phone-update' : 'email-update' ?>">
    <?php if ($is_phone): ?>
      <!-- POST /user/phone_update/ {phone} -->
      <div class="form__row">
        <label class="form__label" for="contact-phone">Новий телефон</label>
        <input class="form__input" type="tel" id="contact-phone" name="phone"
               autocomplete="tel" placeholder="+380 63 015 05 17" required>
      </div>
    <?php else: ?>
      <!-- POST /user/email_update/ {email, next_url} -->
      <div class="form__row">
        <label class="form__label" for="contact-email">Нова пошта</label>
        <input class="form__input" type="email" id="contact-email" name="email"
               autocomplete="email" inputmode="email" placeholder="iryna@example.com" required>
      </div>
      <input type="hidden" name="next_url" value="<?= htmlspecialchars($next_url) ?>">
    <?php endif; ?>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">
        <?= $is_phone ? 'Надіслати код' : 'Надіслати посилання' ?>
      </button>
      <button type="button" class="btn btn--outlined" data-modal-close>Скасувати</button>
    </div>
  </form>

<?php endif; ?>
