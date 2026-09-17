<?php
/**
 * Відкриває сторінку кабінету: хлібні крихти, шапка з імʼям і балансом,
 * сітка [бокова навігація | контент]. Закривається layout-close.php.
 *
 * Перед include задай:
 *   $account_nav    — ключ активного розділу (див. nav.php)
 *   $account_title  — заголовок розділу (H1)
 *
 * Гостьовий стан обробляється тут, а не на кожній сторінці: незалогінений
 * бачить запрошення увійти замість кабінету, і всі п'ять розділів
 * поводяться однаково. На WP цю роль візьме гард template_redirect
 * (ТЗ §7.1) — тоді ця гілка стане підстраховкою.
 */
$account_nav   = $account_nav ?? '';
$account_title = $account_title ?? 'Кабінет';
$is_logged     = account_is_logged();
$profile       = $is_logged ? account_profile() : null;
?>
<section class="page-open">
  <div class="container">
    <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
      <a href="index.php">Головна</a>
      <span aria-hidden="true">·</span>
      <?php if ($account_nav === 'profile' || !$is_logged): ?>
        <span aria-current="page">Кабінет</span>
      <?php else: ?>
        <a href="account.php">Кабінет</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page"><?= htmlspecialchars($account_title) ?></span>
      <?php endif; ?>
    </nav>

    <?php if (!$is_logged): ?>
      <div class="account-guest">
        <h1 class="account-guest__title" data-reveal>Кабінет клієнта</h1>
        <p class="text--lead text--muted account-guest__text" data-reveal style="--reveal-i: 1">
          Тут ваші абонементи, записи на заняття та історія оплат. Увійдіть за
          номером телефону — якщо ви ще не займались у нас, акаунт створиться сам.
        </p>
        <div class="account-guest__actions" data-reveal style="--reveal-i: 2">
          <button type="button" class="btn btn--filled" data-modal="auth">Увійти</button>
          <a class="btn btn--outlined" href="schedule.php">Подивитись розклад</a>
        </div>
      </div>
    <?php else: ?>
      <div class="account-head">
        <div class="account-head__who">
          <h1 class="account-head__name"><?= htmlspecialchars($profile['name']) ?></h1>
        </div>
        <p class="account-head__balance">
          <span class="label text--muted">На рахунку</span>
          <span class="account-head__sum"><?= format_price($profile['account']) ?></span>
        </p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($is_logged): ?>
<section class="section section--tight-top">
  <div class="container">
    <div class="account-layout">
      <?php include __DIR__ . '/nav.php'; ?>

      <div class="account-content">
        <h2 class="account-content__title"><?= htmlspecialchars($account_title) ?></h2>
<?php endif; ?>
