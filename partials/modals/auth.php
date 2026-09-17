<?php
/**
 * Вхід у кабінет за телефоном. Тригер — [data-modal="auth"] або
 * ?modal=auth (на нього ж редіректитиме гард кабінету на WP, ТЗ §7.1).
 *
 * Телефон обраний над email навмисно: у телефонного флоу КОЖЕН крок —
 * задокументований ендпоінт з явним кодом підтвердження, який клієнт
 * вводить у нашій формі. В email-флоу (email_login_signup/
 * email_reset_password) підтвердження — лист із посиланням, чийого
 * формату схема не описує (немає email_verify), тож зробити його надійно
 * зараз не можна. Email лишається в профілі як довідковий контакт
 * (partials/modals/contact-change.php), але не як спосіб входу.
 *
 * Клієнт НЕ обирає «вхід чи реєстрація» — POST /auth/phone_login_signup/
 * робить і те, і те, а розрізняє їх HTTP-код відповіді:
 *   200 → клієнт існує, пароль правильний, віддає токени
 *   201 → новий клієнт створений, SMS з кодом пішла
 *   401 → заблокований (is_active=False) або невірний пароль
 *   409 → integrity_error, колізія при створенні
 *
 * Стани перемикаються через ?step=:
 *   (порожньо) — телефон + пароль
 *   code       — код із SMS (прийшла відповідь 201)
 *   reset      — скидання пароля: POST /auth/phone_reset_password/ {phone} → SMS
 *   reset-code — код + новий пароль: POST /auth/phone_reset_password_verify/
 *                {phone, code, password} → 200 {token, refresh}, клієнт
 *                одразу залогінений
 */
$step  = $_GET['step'] ?? '';
$phone = $_GET['phone'] ?? '';
// from=account — та сама модалка, відкрита з account-password.php уже
// залогіненим клієнтом: «Назад до входу» там безглузде, і телефон уже
// підставлений із профілю, тож поле для нього ховаємо.
$from_account = ($_GET['from'] ?? '') === 'account';
?>
<?php if ($step === 'code'): ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Підтвердіть номер</h2>
    <p class="text text--muted mt-3">
      Ми надіслали код на <?= htmlspecialchars($phone ?: 'ваш номер') ?>.
      Введіть його — і ми відкриємо кабінет.
    </p>
  </div>

  <!-- POST /auth/phone_verify/ {phone, code} -->
  <form class="form modal__body" action="" method="post" data-remote data-auth="verify">
    <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
    <div class="form__row">
      <label class="form__label" for="auth-code">Код із SMS</label>
      <input class="form__input" type="text" id="auth-code" name="code"
             inputmode="numeric" autocomplete="one-time-code" placeholder="0000" required>
    </div>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">Підтвердити</button>
      <button type="button" class="btn btn--outlined" data-modal="auth">Змінити номер</button>
    </div>
  </form>

<?php elseif ($step === 'reset'): ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">
      <?= $from_account ? 'Змінити пароль' : 'Відновити пароль' ?>
    </h2>
    <p class="text text--muted mt-3">
      <?= $from_account
        ? 'Надішлемо код на ' . htmlspecialchars($phone ?: 'ваш номер') . ' — і ви одразу задасте новий пароль.'
        : 'Введіть номер телефону — надішлемо код, і ви одразу задасте новий пароль.' ?>
    </p>
  </div>

  <!-- POST /auth/phone_reset_password/ {phone} -->
  <form class="form modal__body" action="" method="post" data-remote data-auth="reset">
    <?php if ($from_account): ?>
      <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
    <?php else: ?>
      <div class="form__row">
        <label class="form__label" for="auth-reset-phone">Телефон</label>
        <input class="form__input" type="tel" id="auth-reset-phone" name="phone"
               autocomplete="tel" placeholder="+380 63 015 05 17"
               value="<?= htmlspecialchars($phone) ?>" required>
      </div>
    <?php endif; ?>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">Надіслати код</button>
      <?php if ($from_account): ?>
        <button type="button" class="btn btn--outlined" data-modal-close>Скасувати</button>
      <?php else: ?>
        <button type="button" class="btn btn--outlined" data-modal="auth">Назад до входу</button>
      <?php endif; ?>
    </div>
  </form>

<?php elseif ($step === 'reset-code'): ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Новий пароль</h2>
    <p class="text text--muted mt-3">
      Код прийшов на <?= htmlspecialchars($phone ?: 'ваш номер') ?>. Введіть
      його разом із новим паролем.
    </p>
  </div>

  <!-- POST /auth/phone_reset_password_verify/ {phone, code, password}
       → 200 {token, refresh}: пароль змінено й клієнт одразу залогінений,
       окремого входу після цього не потрібно. -->
  <form class="form modal__body" action="" method="post" data-remote data-auth="reset-verify">
    <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
    <?php if ($from_account): ?>
      <!-- Бекенду: після 200 з новими токенами повертати клієнта в кабінет,
           а не на вхід — він і так залогінений. -->
      <input type="hidden" name="from" value="account">
    <?php endif; ?>
    <div class="form__row">
      <label class="form__label" for="auth-reset-code">Код із SMS</label>
      <input class="form__input" type="text" id="auth-reset-code" name="code"
             inputmode="numeric" autocomplete="one-time-code" placeholder="0000" required>
    </div>
    <div class="form__row">
      <label class="form__label" for="auth-reset-password">Новий пароль</label>
      <input class="form__input" type="password" id="auth-reset-password" name="password"
             autocomplete="new-password" minlength="6" required>
    </div>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">Зберегти пароль</button>
      <button type="button" class="btn btn--outlined" data-modal="auth?step=reset<?= $from_account ? '&from=account&phone=' . rawurlencode($phone) : '' ?>">Надіслати код ще раз</button>
    </div>
  </form>

<?php else: ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Вхід у кабінет</h2>
    <p class="text text--muted mt-3">
      Введіть номер телефону й пароль. Якщо ви ще не займались у нас —
      акаунт створиться сам, ми надішлемо код підтвердження.
    </p>
  </div>

  <!-- POST /auth/phone_login_signup/ {phone, password} -->
  <form class="form modal__body" action="" method="post" data-remote data-auth="login">
    <div class="form__row">
      <label class="form__label" for="auth-phone">Телефон</label>
      <input class="form__input" type="tel" id="auth-phone" name="phone"
             autocomplete="tel" placeholder="+380 63 015 05 17" required>
    </div>

    <div class="form__row">
      <label class="form__label" for="auth-password">Пароль</label>
      <input class="form__input" type="password" id="auth-password" name="password"
             autocomplete="current-password" minlength="6" required>
      <p class="text--xs">
        <button type="button" class="link-button text--muted" data-modal="auth?step=reset">Забули пароль?</button>
      </p>
    </div>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">Далі</button>
      <a class="btn btn--outlined" href="tel:+380630150517">Подзвонити</a>
    </div>
  </form>

<?php endif; ?>
