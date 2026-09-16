<?php
header('Cache-Control: no-store');   // §3 — кабінет не кешується

require_once __DIR__ . '/api/account.php';

$nav = '';
$page_title = 'Абонементи — кабінет клієнта';
$page_description = 'Активні та архівні абонементи клієнта студії «Пілатес Львів».';

$account_nav   = 'cards';
$account_title = 'Абонементи';

// Вкладка — окремим URL, а не JS-табами: розділи кабінету теж окремі
// сторінки, і посилання на архів має бути чим поділитись/перезавантажити.
$is_archive = ($_GET['tab'] ?? '') === 'archive';
$cards = account_cards($is_archive ? 3 : 1);

include 'partials/header.php';
?>

<?php include 'partials/account/layout-open.php'; ?>

<?php if (account_is_logged()): ?>
  <nav class="filters mb-7" aria-label="Фільтр абонементів">
    <a class="btn btn--sm btn--tab<?= $is_archive ? '' : ' is-current' ?>"
       href="account-cards.php"<?= $is_archive ? '' : ' aria-current="page"' ?>>
      Активні <span class="filters__count"><?= count(account_cards(1)) ?></span>
    </a>
    <a class="btn btn--sm btn--tab<?= $is_archive ? ' is-current' : '' ?>"
       href="account-cards.php?tab=archive"<?= $is_archive ? ' aria-current="page"' : '' ?>>
      Архів <span class="filters__count"><?= count(account_cards(3)) ?></span>
    </a>
  </nav>

  <?php if (!$cards): ?>
    <?php
    $empty_text = $is_archive
      ? 'В архіві поки нічого немає.'
      : 'Активних абонементів немає.';
    if (!$is_archive) {
      $empty_link_href = 'prices.php';
      $empty_link_label = 'Подивитись тарифи';
    }
    include 'partials/account/empty.php';
    ?>
  <?php else: ?>
    <ul class="card-list">
      <?php foreach ($cards as $card): ?>
        <?php
        $left = card_left($card);
        $amount = (int)$card['amount'];
        // Прогрес малюємо лише для лічильних абонементів: у безлімітного
        // немає «скільки з скількох», смужка на весь рядок брехала б.
        $has_progress = $amount > 0;
        $used = min((int)$card['used'], $amount);
        ?>
        <li class="card-item">
          <div class="card-item__head">
            <h3 class="card-item__title"><?= htmlspecialchars($card['title']) ?></h3>
            <span class="badge <?= $is_archive ? 'badge--muted' : ((int)$card['status'] === 4 ? 'badge--ok' : 'badge--warn') ?>">
              <?= htmlspecialchars($card['status_detail']) ?>
            </span>
          </div>

          <?php if ($has_progress): ?>
            <div class="card-item__progress">
              <div class="card-item__bar">
                <span class="card-item__bar-fill" style="--fill: <?= round($used / $amount * 100) ?>%"></span>
              </div>
              <p class="text--sm">
                Використано <?= $used ?> з <?= $amount ?><?php if ($left > 0): ?>, лишилось <?= $left ?><?php endif; ?>
              </p>
            </div>
          <?php else: ?>
            <p class="card-item__unlimited">Без обмежень за кількістю занять</p>
          <?php endif; ?>

          <dl class="card-item__facts">
            <div class="card-item__fact">
              <dt class="label text--muted">Діє</dt>
              <dd><?= format_date_long($card['date_start'], false) ?> — <?= format_date_long($card['due_date'], false) ?></dd>
            </div>
            <div class="card-item__fact">
              <dt class="label text--muted">Оплата</dt>
              <dd><?= htmlspecialchars($card['paid_detail']) ?></dd>
            </div>
            <div class="card-item__fact">
              <dt class="label text--muted">Вартість</dt>
              <dd><?= format_price($card['price']) ?></dd>
            </div>
            <?php if ((int)$card['transfer'] > 0): ?>
              <div class="card-item__fact">
                <dt class="label text--muted">Перенесення</dt>
                <dd><?= (int)$card['transfer'] ?> <?= plural_uk((int)$card['transfer'], 'заняття', 'заняття', 'занять') ?></dd>
              </div>
            <?php endif; ?>
            <?php if ((int)$card['pauses'] > 0): ?>
              <div class="card-item__fact">
                <dt class="label text--muted">Заморозка</dt>
                <dd><?= (int)$card['pauses'] ?> × <?= (int)$card['paused_duration'] ?> <?= plural_uk((int)$card['paused_duration'], 'день', 'дні', 'днів') ?></dd>
              </div>
            <?php endif; ?>
          </dl>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (!$is_archive): ?>
    <p class="mt-7">
      <a class="btn btn--filled" href="prices.php">Придбати абонемент</a>
    </p>
  <?php endif; ?>
<?php endif; ?>

<?php include 'partials/account/layout-close.php'; ?>

<?php include 'partials/footer.php'; ?>
