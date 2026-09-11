<?php
/**
 * Деталі заняття. Тригер — [data-modal="slot-details?event=<id>"], і в
 * рядку розкладу (.schedule__slot-actions), і в клітинці .schedule-month.
 * Окремий точковий запит до API (fetch_event_by_id), а не пошук по вже
 * завантаженому діапазону дат зі схеми — доступність/ціна лишаються
 * актуальними на момент відкриття модалки, а не на момент рендеру рядка.
 *
 * TODO (WP): фото банера захардкоджене, аватар тренера — плейсхолдер.
 * InstaSport не віддає ні зображення заняття, ні фото тренера (лише
 * id+name), а локальний $team мапиться по імені неоднозначно — двоє
 * тренерів звуться «Вікторія». На WP і те, й те приходить із CMS.
 */
require_once __DIR__ . '/../../api/schedule.php';

$event_id = (int)($_GET['event'] ?? 0);
$tz = new DateTimeZone('Europe/Kyiv');
$found = $event_id ? fetch_event_by_id($event_id) : null;
$sch_slot = $found ? denormalize($found['event'], $found['refs'], $tz) : null;

if ($sch_slot) {
  [$d_h, $d_m] = array_pad(explode(':', (string)$sch_slot['duration']), 2, 0);
  $duration_min = (int)$d_h * 60 + (int)$d_m;
  // no_seats — конкретно 0 місць; blocked — ширше, включає й enroll_deadline/
  // минулий час. Той самий підхід, що в partials/schedule-slot.php.
  $no_seats = $sch_slot['seats'] === 0;
  $blocked  = !$sch_slot['is_bookable'];
  $block_label = $no_seats ? 'Немає місць' : 'Недоступно';

  // Дата словами — «14 вересня, понеділок» читабельніше за 2026-09-14.
  // Зона — через icu_tz_name(), а не $tz->getName(): на старому ICU
  // Europe/Kyiv немає, і форматер падає (див. api/schedule.php).
  $slot_date = new DateTimeImmutable($sch_slot['date'], $tz);
  $date_label = (new IntlDateFormatter('uk_UA', IntlDateFormatter::NONE, IntlDateFormatter::NONE,
    icu_tz_name(), null, 'd MMMM, EEEE'))->format($slot_date);
}
?>
<?php if (!$sch_slot): ?>
  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Заняття не знайдено</h2>
  </div>
  <div class="modal__body">
    <p class="text--body text--muted">
      Це заняття вже минуло або недоступне. Оберіть інше в розкладі.
    </p>
  </div>
<?php else: ?>
  <div class="slot-detail">
    <div class="slot-detail__media">
      <img src="assets/img/directions/2.jpeg" alt=""
           width="1792" height="2400" loading="lazy">
    </div>

    <div class="slot-detail__body">
      <div class="slot-detail__head">
        <?php if ($sch_slot['direction']): ?>
          <span class="label slot-detail__kicker"><?= htmlspecialchars($sch_slot['direction']) ?></span>
        <?php endif; ?>
        <h2 class="modal__title" id="modal-overlay-title"><?= htmlspecialchars($sch_slot['title']) ?></h2>
        <p class="slot-detail__when"><?= htmlspecialchars($date_label) ?></p>
      </div>

      <ul class="slot-detail__facts">
        <li class="slot-detail__fact">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-calendar"></use></svg>
          <?= htmlspecialchars($sch_slot['time']) ?><?= $duration_min ? ' · ' . $duration_min . ' хв' : '' ?>
        </li>
        <?php if ($sch_slot['location']): ?>
          <li class="slot-detail__fact">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
            <?= htmlspecialchars($sch_slot['location']) ?>
          </li>
        <?php endif; ?>
        <li class="slot-detail__fact<?= $no_seats ? ' is-muted' : '' ?>">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-user"></use></svg>
          <?= $no_seats ? 'Місць немає' : ($sch_slot['seats'] ? $sch_slot['seats'] . ' місць' : 'Місця є') ?>
        </li>
      </ul>

      <?php if ($sch_slot['trainers']): ?>
        <a class="slot-detail__trainer" href="trainer-single.php?trainer=halyna">
          <span class="slot-detail__ava">
            <svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-user"></use></svg>
          </span>
          <span class="slot-detail__trainer-text">
            <span class="label text--muted">Заняття веде</span>
            <span class="slot-detail__trainer-name">
              <?= htmlspecialchars(implode(', ', array_column($sch_slot['trainers'], 'name'))) ?>
            </span>
          </span>
        </a>
      <?php endif; ?>

      <div class="slot-detail__foot">
        <?php if ($sch_slot['price']): ?>
          <span class="slot-detail__price"><?= (int)$sch_slot['price'] ?> грн</span>
        <?php endif; ?>
        <?php if ($blocked): ?>
          <button type="button" class="btn btn--filled" disabled><?= $block_label ?></button>
        <?php else: ?>
          <button type="button" class="btn btn--filled" data-modal="booking?event=<?= $sch_slot['id'] ?>">Записатись</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
