<?php
/**
 * Запис на заняття. Тригери:
 *   data-modal="booking"                — загальна заявка («передзвоніть мені»)
 *   data-modal="booking?direction=yoga" — те саме, з підставленим напрямком
 *   data-modal="booking?event=123"      — запис на конкретне заняття з розкладу
 *
 * Модалка двостанна (ТЗ §10):
 *   гість         → заявка на дзвінок (фолбек через public/user_create/,
 *                   він не вимагає авторизації взагалі)
 *   залогінений + конкретне заняття → реальні способи оплати
 *
 * Список способів оплати рахує booking_payment_options() (api/account.php):
 * абонементи фільтруються по event.template.card_templates (§1.2), інакше
 * клієнт побачив би абонемент на йогу як спосіб оплатити реформер.
 *
 * Бекенду ще немає — action порожній, форми позначені [data-remote], щоб
 * глобальний перехоплювач .form у js/main.js не підмінив сабміт модалкою
 * подяки (§12).
 */
require_once __DIR__ . '/../../api/account.php';

$direction = $_GET['direction'] ?? '';
$event_id  = (int)($_GET['event'] ?? 0);
$is_logged = account_is_logged();

$directions = [
  'pilates'  => 'Пілатес на обладнанні',
  'recovery' => 'Функціональне відновлення',
  'yoga'     => 'Йога',
  'dance'    => 'Танці',
  'physio'   => 'Консультація фізіолога',
  'academy'  => 'Навчальний центр',
];

// Заняття підтягуємо лише коли воно справді передане: загальна заявка
// не має до чого звертатись, і зайвий запит до API їй не потрібен.
$bk_slot = null;
if ($event_id) {
  require_once __DIR__ . '/../../api/schedule.php';
  $found = fetch_event_by_id($event_id);
  $bk_slot = $found ? denormalize($found['event'], $found['refs'], new DateTimeZone(icu_tz_name())) : null;
}

$show_payment = $is_logged && $bk_slot && $bk_slot['is_bookable'];
$options = $show_payment ? booking_payment_options($bk_slot) : [];
?>

<?php if ($bk_slot && !$bk_slot['is_bookable']): ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Запис закритий</h2>
    <p class="text text--muted mt-3">
      <?= $bk_slot['seats'] === 0
        ? 'На це заняття вже немає вільних місць.'
        : 'Час запису на це заняття минув.' ?>
      Оберіть інше в розкладі — або лишіть телефон, і ми підберемо заміну.
    </p>
  </div>

  <div class="modal__foot">
    <a class="btn btn--filled" href="schedule.php">Відкрити розклад</a>
    <button type="button" class="btn btn--outlined" data-modal="callback">Передзвоніть мені</button>
  </div>

<?php elseif ($show_payment): ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title"><?= htmlspecialchars($bk_slot['title']) ?></h2>
    <p class="text text--muted mt-3">
      <?= format_slot_datetime($bk_slot) ?>
      <?php if ($bk_slot['location']): ?> · <?= htmlspecialchars($bk_slot['location']) ?><?php endif; ?>
      <?php if (!empty($bk_slot['trainers'][0]['name'])): ?> · <?= htmlspecialchars($bk_slot['trainers'][0]['name']) ?><?php endif; ?>
    </p>
  </div>

  <form class="form modal__body" action="" method="post" data-remote>
    <input type="hidden" name="event" value="<?= (int)$bk_slot['id'] ?>">

    <fieldset class="form__radios">
      <legend class="form__label">Чим оплатити</legend>
      <?php foreach ($options as $i => $opt): ?>
        <label class="form__radio">
          <input type="radio" name="payment_type" value="<?= $opt['type'] ?>"
                 <?= isset($opt['card']) ? 'data-card="' . (int)$opt['card'] . '"' : '' ?>
                 <?= $i === 0 ? 'checked' : '' ?>>
          <span class="form__radio-body">
            <span class="form__radio-title"><?= htmlspecialchars($opt['title']) ?></span>
            <?php if ($opt['note']): ?>
              <span class="form__radio-note text--sm text--muted"><?= htmlspecialchars($opt['note']) ?></span>
            <?php endif; ?>
          </span>
        </label>
      <?php endforeach; ?>
    </fieldset>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">Записатись</button>
      <a class="btn btn--outlined" href="schedule.php">Обрати інший час</a>
    </div>
  </form>

<?php else: ?>

  <div class="modal__head">
    <h2 class="modal__title" id="modal-overlay-title">Записатися на тренування</h2>
    <p class="text text--muted mt-3">
      <?php if ($bk_slot): ?>
        <?= htmlspecialchars($bk_slot['title']) ?>,
        <?= format_slot_datetime($bk_slot) ?>.
        Лишіть телефон — запишемо вас і підтвердимо місце.
      <?php else: ?>
        Передзвонимо, підберемо час і відповімо на питання. Перше заняття — знайомство.
      <?php endif; ?>
    </p>
  </div>

  <form class="form modal__body" action="" method="post" data-remote>
    <?php if ($bk_slot): ?>
      <input type="hidden" name="event" value="<?= (int)$bk_slot['id'] ?>">
    <?php endif; ?>

    <div class="form__row">
      <label class="form__label" for="booking-name">Ім’я</label>
      <input class="form__input" type="text" id="booking-name" name="name" autocomplete="name"
             placeholder="Ірина" required>
    </div>

    <div class="form__row">
      <label class="form__label" for="booking-phone">Телефон</label>
      <input class="form__input" type="tel" id="booking-phone" name="phone" autocomplete="tel"
             placeholder="+380 63 015 05 17" required>
    </div>

    <?php if (!$bk_slot): ?>
      <div class="form__row">
        <label class="form__label" for="booking-direction">Напрямок</label>
        <select class="form__input" id="booking-direction" name="direction" data-slimselect>
          <option value="" data-placeholder="true">Ще не обрав</option>
          <?php foreach ($directions as $key => $label): ?>
            <option value="<?= $key ?>"<?= $key === $direction ? ' selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>

    <p class="text--sm text--muted">
      Уже займаєтесь у нас?
      <button type="button" class="link-button" data-modal="auth">Увійдіть</button> —
      тоді можна записатись одразу з абонемента.
    </p>

    <div class="modal__foot">
      <button type="submit" class="btn btn--filled">Записатись</button>
      <a class="btn btn--outlined" href="tel:+380630150517">Подзвонити</a>
    </div>
  </form>

<?php endif; ?>
