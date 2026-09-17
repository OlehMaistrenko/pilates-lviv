<?php
header('Cache-Control: no-store');   // §3 — кабінет не кешується

require_once __DIR__ . '/api/account.php';

$nav = '';
$page_title = 'Зміна паролю — кабінет клієнта';
$page_description = 'Зміна пароля для входу в кабінет.';

$account_nav   = 'password';
$account_title = 'Зміна паролю';

/**
 * Окремого ендпоінта «змінити пароль» в InstaSport немає: PATCH /user/
 * (PatchedUser) полем password не приймає, а весь пароль живе в /auth/*.
 * Тому зміна пароля залогіненого клієнта — це той самий двокроковий флоу
 * скидання, що вже стоїть у модалці входу:
 *
 *   POST /auth/phone_reset_password/        {phone*}                → 200, SMS
 *   POST /auth/phone_reset_password_verify/ {phone*, code*, password*}
 *        → 200 {token, refresh}
 *
 * Перший крок тут — сама кнопка: телефон уже є в профілі, вводити нічого,
 * тож окремий екран під нього не потрібен. Модалка відкривається одразу на
 * другому кроці (partials/modals/auth.php?step=reset-code), де є що
 * заповнювати — код і новий пароль; from=account каже бекенду повернути
 * клієнта після зміни в кабінет, а не на вхід.
 *
 * Наслідок для бекенду: _verify/ віддає НОВУ пару токенів — стару в сесії
 * треба замінити, інакше подальші запити кабінету підуть із простроченим
 * токеном.
 *
 * Старий пароль API не питає — підтвердженням служить SMS-код, тож поля
 * «поточний пароль» тут свідомо немає.
 */

include 'partials/header.php';
?>

<?php include 'partials/account/layout-open.php'; ?>

<?php if (account_is_logged()): ?>
  <?php $profile = account_profile(); ?>

  <p class="text--sm text--muted">
    Пароль міняється через код у SMS: надішлемо його на
    <?= htmlspecialchars($profile['phone'] ?: 'ваш номер') ?>, і ви одразу
    задасте новий.
  </p>

  <div class="account-form__actions mt-5">
    <!-- Бекенду: спершу POST /auth/phone_reset_password/ {phone} (хук
         data-auth), і лише на 200 відкривати модалку — тут це вже робить
         window._functions.loadModal(). Поки бекенду немає, [data-modal]
         відкриває її одразу, щоб флоу було видно на верстці.
         rawurlencode: у номері є «+», а URLSearchParams у js/main.js
         розбирає query і без кодування прочитав би його як пробіл. -->
    <button type="button" class="btn btn--filled" data-auth="reset"
            data-phone="<?= htmlspecialchars($profile['phone']) ?>"
            data-modal="auth?step=reset-code&from=account&phone=<?= rawurlencode($profile['phone']) ?>">
      Надіслати код
    </button>
  </div>
<?php endif; ?>

<?php include 'partials/account/layout-close.php'; ?>

<?php include 'partials/footer.php'; ?>
