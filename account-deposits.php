<?php
header('Cache-Control: no-store');   // §3 — кабінет не кешується

require_once __DIR__ . '/api/account.php';

$nav = '';
$page_title = 'Рахунок і поповнення — кабінет клієнта';
$page_description = 'Баланс особистого рахунку та історія поповнень.';

$account_nav   = 'deposits';
$account_title = 'Рахунок і поповнення';

$deposits = account_deposits();

include 'partials/header.php';
?>

<?php include 'partials/account/layout-open.php'; ?>

<?php if (account_is_logged()): ?>
  <?php $profile = account_profile(); ?>

  <div class="account-balance">
    <p class="account-balance__sum"><?= format_price($profile['account']) ?></p>
    <p class="text--sm text--muted account-balance__note">
      З особистого рахунку можна оплатити заняття без абонемента.
    </p>
    <form class="account-balance__form form" action="" method="post" data-remote>
      <div class="form__row account-balance__row">
        <label class="form__label" for="deposit-amount">Поповнити на</label>
        <input class="form__input" type="number" id="deposit-amount" name="amount"
               min="100" step="50" value="500" inputmode="numeric" required>
      </div>
      <button type="submit" class="btn btn--filled">Поповнити</button>
    </form>
  </div>

  <h3 class="account-content__subtitle">Історія</h3>

  <?php if (!$deposits): ?>
    <?php
    $empty_text = 'Поповнень поки не було.';
    include 'partials/account/empty.php';
    ?>
  <?php else: ?>
    <div class="deposit-list">
      <ul class="rows">
        <?php foreach ($deposits as $d): ?>
          <?php $is_charge = (float)$d['amount'] < 0; ?>
          <li class="rows__item rows__item--deposit">
            <span class="rows__num"><?= format_date_long($d['date']) ?></span>
            <span class="rows__title"><?= htmlspecialchars($d['comment']) ?></span>
            <span class="text--sm text--muted"><?= htmlspecialchars($d['paid_detail']) ?></span>
            <span class="deposit__sum<?= $is_charge ? ' deposit__sum--out' : '' ?>">
              <?= $is_charge ? '' : '+' ?><?= format_price($d['amount']) ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php include 'partials/account/layout-close.php'; ?>

<?php include 'partials/footer.php'; ?>
