<?php
header('Cache-Control: no-store');   // §3 — кабінет не кешується

require_once __DIR__ . '/api/account.php';

$nav = '';
$page_title = 'Мої заняття — кабінет клієнта';
$page_description = 'Заплановані та минулі заняття клієнта студії «Пілатес Львів».';

$account_nav   = 'visits';
$account_title = 'Мої заняття';

$is_past = ($_GET['tab'] ?? '') === 'past';
$visits = account_visits($is_past ? 'past' : 'upcoming');

include 'partials/header.php';
?>

<?php include 'partials/account/layout-open.php'; ?>

<?php if (account_is_logged()): ?>
  <nav class="filters mb-7" aria-label="Фільтр занять">
    <a class="btn btn--sm btn--tab<?= $is_past ? '' : ' is-current' ?>"
       href="account-visits.php"<?= $is_past ? '' : ' aria-current="page"' ?>>
      Заплановані <span class="filters__count"><?= count(account_visits('upcoming')) ?></span>
    </a>
    <a class="btn btn--sm btn--tab<?= $is_past ? ' is-current' : '' ?>"
       href="account-visits.php?tab=past"<?= $is_past ? ' aria-current="page"' : '' ?>>
      Історія <span class="filters__count"><?= count(account_visits('past')) ?></span>
    </a>
  </nav>

  <?php if (!$visits): ?>
    <?php
    $empty_text = $is_past
      ? 'Минулих занять поки немає.'
      : 'Запланованих занять немає.';
    if (!$is_past) {
      $empty_link_href = 'schedule.php';
      $empty_link_label = 'Обрати заняття в розкладі';
    }
    include 'partials/account/empty.php';
    ?>
  <?php else: ?>
    <div class="visit-list">
      <ul class="rows">
        <?php foreach ($visits as $visit): ?>
          <?php $can_cancel = visit_is_cancellable($visit); ?>
          <li class="rows__item rows__item--visit">
            <span class="visit__when">
              <span class="rows__num"><?= format_date_long($visit['date']) ?></span>
            </span>
            <span class="rows__title"><?= htmlspecialchars($visit['title']) ?></span>
            <!-- Бейдж статусу («заплановане/відвідане/пропущене/скасоване»)
                 тут раніше стояв на вигаданому полі: у ProfileVisit немає
                 status узагалі (реальні поля — лише authorized: bool і
                 event_date, звірено зі схемою). Показувати нема з чого.
                 paid_detail (справжнє поле — спосіб оплати) додано в цей
                 же рядок, а не окремою колонкою: дві сусідні приглушені
                 клітинки без відмінності у вазі/кольорі зливались одна в
                 одну — тут вони хоч розділені крапкою в одному, звичному
                 читачу форматі (той самий приймач "А · Б", що тренер·локація). -->
            <span class="visit__where text--sm text--muted">
              <?= htmlspecialchars($visit['instructor']) ?> · <?= htmlspecialchars($visit['hall']) ?> · <?= htmlspecialchars($visit['paid_detail']) ?>
            </span>
            <span class="visit__action">
              <?php if ($can_cancel): ?>
                <form action="" method="post" data-remote>
                  <input type="hidden" name="visit" value="<?= (int)$visit['id'] ?>">
                  <button type="submit" class="btn btn--sm btn--outlined">Скасувати</button>
                </form>
              <?php endif; ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <?php if (!$is_past): ?>
      <p class="text--sm text--muted mt-6">
        Скасувати запис можна не пізніше ніж за добу до заняття. Скільки разів —
        залежить від абонемента, з якого списано заняття.
      </p>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>

<?php include 'partials/account/layout-close.php'; ?>

<?php include 'partials/footer.php'; ?>
