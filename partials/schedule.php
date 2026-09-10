<?php
/**
 * Розклад занять — календар день / тиждень / місяць. Перед include задай:
 *   $schedule_trainer   — id тренера: лише його заняття (детальна тренера). Опційно.
 *   $schedule_direction — id напрямку (детальна тренування). Опційно.
 *   $schedule_location  — id локації/hall (детальна локації). Опційно.
 *   $schedule_filters   — true: показати перемикачі локації/напрямку/тренера
 *                         (сторінка розкладу). Дефолт false — на детальній
 *                         сторінці фільтр зайвий, там контекст уже заданий.
 *   $schedule_title     — заголовок секції. '' — секція без шапки.
 *   $schedule_link      — ['текст', 'href'] праворуч у шапці (напр. повний розклад).
 *   $schedule_class     — додатковий клас на <section> (напр. 'pt-0'). Опційно.
 *   $schedule_id        — id секції для якоря. Дефолт 'schedule'.
 *
 * Стан (вид і дата) живе в URL — ?view=week&date=2026-09-10 — тією самою
 * конвенцією, що .filters у blog.php/team.php: працює без JS, індексується,
 * посилання можна зберегти. Дані бере api/schedule.php напряму (require),
 * а не HTTP-запитом сам до себе.
 *
 * Змінні скидаються в кінці.
 */
require_once __DIR__ . '/../api/schedule.php';

$schedule_trainer   = $schedule_trainer   ?? null;
$schedule_direction = $schedule_direction ?? null;
$schedule_location  = $schedule_location  ?? null;
$schedule_filters   = $schedule_filters   ?? false;
$schedule_title     = $schedule_title     ?? 'Розклад занять';
$schedule_link      = $schedule_link      ?? null;
$schedule_class     = $schedule_class     ?? '';
$schedule_id        = $schedule_id        ?? 'schedule';

// Europe/Kyiv — сучасна назва, але в старіших збірках ICU (яку тягне intl)
// її ще немає, і IntlDateFormatter падає з U_ILLEGAL_ARGUMENT_ERROR, хоча
// сам PHP таку зону приймає (бази даних дві й вони розходяться: MAMP несе
// ICU 56, де є лише Europe/Kiev). Пробуємо саме тим викликом, що й нижче —
// з патерном: коротшу форму конструктора старий ICU ковтає без помилки.
$sch_tz_name = 'Europe/Kyiv';
try {
  new IntlDateFormatter('uk_UA', IntlDateFormatter::NONE, IntlDateFormatter::NONE, $sch_tz_name, null, 'd');
} catch (Throwable) {
  $sch_tz_name = 'Europe/Kiev';
}

$sch_tz    = new DateTimeZone($sch_tz_name);
$sch_today = new DateTimeImmutable('today', $sch_tz);

// Вид і дата з URL; невідоме значення — фолбек, а не 500.
$sch_view = $_GET['view'] ?? 'week';
if (!in_array($sch_view, ['day', 'week', 'month'], true)) $sch_view = 'week';

$sch_date = valid_date($_GET['date'] ?? '');
$sch_date = $sch_date ? new DateTimeImmutable($sch_date, $sch_tz) : $sch_today;

// Фільтри з URL діють лише там, де показані перемикачі. На детальній
// сторінці контекст задано змінною включення, і ?trainer= його не перебиває.
if ($schedule_filters) {
  $schedule_trainer   = ($_GET['trainer']   ?? '') !== '' ? (int)$_GET['trainer']   : $schedule_trainer;
  $schedule_direction = ($_GET['direction'] ?? '') !== '' ? (int)$_GET['direction'] : $schedule_direction;
  $schedule_location  = ($_GET['location']  ?? '') !== '' ? (int)$_GET['location']  : $schedule_location;
}

// Межі періоду. Тиждень і місяць — з понеділка: у нас тиждень починається
// з нього, а не з неділі, як у дефолті ISO-8601 йому й відповідає.
[$sch_from, $sch_to] = match ($sch_view) {
  'day'   => [$sch_date, $sch_date],
  'week'  => [$sch_date->modify('monday this week'), $sch_date->modify('monday this week')->modify('+6 days')],
  'month' => [
    $sch_date->modify('first day of this month')->modify('monday this week'),
    // сітка місяця добивається до неділі останнього тижня, тож хвіст може
    // залізти в наступний місяць — це нормально для календаря
    $sch_date->modify('last day of this month')->modify('sunday this week'),
  ],
};

$sch_data = schedule_query([
  'start_date' => $sch_from->format('Y-m-d'),
  'end_date'   => $sch_to->format('Y-m-d'),
  'trainer'    => $schedule_trainer,
  'direction'  => $schedule_direction,
  'location'   => $schedule_location,
]);

// Групуємо по днях — усі три види розкладають однаково, різниця лише в
// тому, які дні показуємо і як їх малюємо.
$sch_by_day = [];
foreach ($sch_data['results'] as $sch_slot) {
  $sch_by_day[$sch_slot['date']][] = $sch_slot;
}

$sch_refs = $sch_data['refs'];

// Форматери: intl є на сервері, тож свій масив місяців не пишемо.
// Зона — з $sch_tz_name, а не літералом: на старому ICU це Europe/Kiev.
$sch_fmt = fn(string $pattern, DateTimeInterface $d) =>
  (new IntlDateFormatter('uk_UA', IntlDateFormatter::NONE, IntlDateFormatter::NONE,
    $sch_tz_name, null, $pattern))->format($d);

// Посилання зі збереженням решти стану: міняємо один параметр, інші несемо далі.
$sch_url = function (array $override) use ($sch_view, $sch_date, $schedule_filters,
                                           $schedule_trainer, $schedule_direction, $schedule_location) {
  $q = ['view' => $sch_view, 'date' => $sch_date->format('Y-m-d')];
  if ($schedule_filters) {
    if ($schedule_trainer)   $q['trainer']   = $schedule_trainer;
    if ($schedule_direction) $q['direction'] = $schedule_direction;
    if ($schedule_location)  $q['location']  = $schedule_location;
  }
  $q = array_filter(array_merge($q, $override), fn($v) => $v !== null && $v !== '');
  return '?' . http_build_query($q);
};

// Крок «назад/вперед» залежить від виду
$sch_step = ['day' => '1 day', 'week' => '1 week', 'month' => '1 month'][$sch_view];
$sch_prev = $sch_date->modify('-' . $sch_step);
$sch_next = $sch_date->modify('+' . $sch_step);

// Підпис поточного періоду
$sch_caption = match ($sch_view) {
  'day'   => $sch_fmt('d MMMM y', $sch_date),
  'week'  => $sch_fmt('d MMM', $sch_from) . ' — ' . $sch_fmt('d MMM y', $sch_from->modify('+6 days')),
  'month' => $sch_fmt('LLLL y', $sch_date),
};

$sch_views = ['day' => 'День', 'week' => 'Тиждень', 'month' => 'Місяць'];
?>
<section class="section schedule<?= $schedule_class ? ' ' . $schedule_class : '' ?>" id="<?= $schedule_id ?>">
  <div class="container">
    <?php if ($schedule_title): ?>
      <div class="section-head section-head--split">
        <h2 data-reveal="lines"><?= $schedule_title ?></h2>
        <?php if ($schedule_link): ?>
          <a class="btn btn--outlined" href="<?= $schedule_link[1] ?>" data-reveal><?= $schedule_link[0] ?></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ($schedule_filters): ?>
      <?php /* GET-форма, а не посилання: стан так само лишається в URL
               (?location=2&direction=1), тож відфільтрований розклад можна
               відкрити напряму й зберегти, і без JS усе працює кнопкою.
               Класу .form на формі бути НЕ МОЖЕ: js/main.js перехоплює
               submit будь-якої .form і відкриває «дякуємо» — GET би нікуди
               не пішов. Поля лишаються .form__input: SlimSelect копіює клас
               із <select> на свій .ss-main, тож вигляд той самий, що в
               модалках. */ ?>
      <form class="schedule-filters" method="get" data-schedule-filters>
        <?php /* вид і дата мусять пережити сабміт фільтра, інакше календар
                 стрибне на дефолтний тиждень */ ?>
        <input type="hidden" name="view" value="<?= $sch_view ?>">
        <input type="hidden" name="date" value="<?= $sch_date->format('Y-m-d') ?>">

        <div class="schedule-filters__row">
          <label class="form__label" for="<?= $schedule_id ?>-location">Локація</label>
          <select class="form__input" id="<?= $schedule_id ?>-location" name="location" data-slimselect>
            <option value="">Усі локації</option>
            <?php foreach ($sch_refs['halls'] as $sch_hid => $sch_hname): ?>
              <option value="<?= $sch_hid ?>"<?= $schedule_location === $sch_hid ? ' selected' : '' ?>><?= $sch_hname ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="schedule-filters__row">
          <label class="form__label" for="<?= $schedule_id ?>-direction">Напрямок</label>
          <select class="form__input" id="<?= $schedule_id ?>-direction" name="direction" data-slimselect>
            <option value="">Усі напрямки</option>
            <?php foreach ($sch_refs['activities'] as $sch_aid => $sch_aname): ?>
              <option value="<?= $sch_aid ?>"<?= $schedule_direction === $sch_aid ? ' selected' : '' ?>><?= $sch_aname ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="schedule-filters__row">
          <label class="form__label" for="<?= $schedule_id ?>-trainer">Тренер</label>
          <select class="form__input" id="<?= $schedule_id ?>-trainer" name="trainer" data-slimselect>
            <option value="">Усі тренери</option>
            <?php foreach ($sch_refs['instructors'] as $sch_iid => $sch_iname): ?>
              <option value="<?= $sch_iid ?>"<?= $schedule_trainer === $sch_iid ? ' selected' : '' ?>><?= $sch_iname ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php /* Без JS — єдиний спосіб застосувати фільтр, тож кнопка
                 справжня. З JS зміна селекта сабмітить форму сама, а кнопку
                 ховаємо (js/main.js) — вона стає зайвим кроком. */ ?>
        <button type="submit" class="btn btn--filled schedule-filters__apply">Показати</button>

        <?php if ($schedule_location || $schedule_direction || $schedule_trainer): ?>
          <a class="link-arrow schedule-filters__reset" href="<?= $sch_url(['location' => null, 'direction' => null, 'trainer' => null]) ?>">Скинути</a>
        <?php endif; ?>
      </form>
    <?php endif; ?>

    <div class="schedule__bar">
      <nav class="filters schedule__views" aria-label="Вид календаря">
        <?php foreach ($sch_views as $sch_k => $sch_label): ?>
          <a class="filters__item<?= $sch_view === $sch_k ? ' is-current' : '' ?>"
             href="<?= $sch_url(['view' => $sch_k]) ?>"<?= $sch_view === $sch_k ? ' aria-current="page"' : '' ?>><?= $sch_label ?></a>
        <?php endforeach; ?>
      </nav>

      <div class="schedule__nav">
        <a class="btn btn--icon btn--sm btn--outlined schedule__arrow schedule__arrow--prev"
           href="<?= $sch_url(['date' => $sch_prev->format('Y-m-d')]) ?>" aria-label="Попередній період" rel="prev">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </a>
        <p class="schedule__caption"><?= $sch_caption ?></p>
        <a class="btn btn--icon btn--sm btn--outlined"
           href="<?= $sch_url(['date' => $sch_next->format('Y-m-d')]) ?>" aria-label="Наступний період" rel="next">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </a>
        <?php if ($sch_date->format('Y-m-d') !== $sch_today->format('Y-m-d')): ?>
          <a class="btn btn--sm btn--outlined schedule__today" href="<?= $sch_url(['date' => $sch_today->format('Y-m-d')]) ?>">Сьогодні</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!$sch_data['count']): ?>
      <p class="text--lead text--muted schedule__empty">
        На цей період занять немає. Спробуйте інший тиждень або
        <button type="button" class="link-arrow" data-modal="booking">запишіться — підберемо час</button>.
      </p>

    <?php elseif ($sch_view === 'month'): ?>
      <?php
      // Сітка місяця. Під 768px вона нечитабельна (7 колонок × 4 символи),
      // тому CSS ховає її й показує .schedule__list нижче — той самий .rows.
      $sch_cursor = $sch_from;
      ?>
      <div class="schedule-month" data-reveal>
        <?php foreach (['Пн','Вт','Ср','Чт','Пт','Сб','Нд'] as $sch_wd): ?>
          <span class="label schedule-month__wd" aria-hidden="true"><?= $sch_wd ?></span>
        <?php endforeach; ?>

        <?php while ($sch_cursor <= $sch_to): ?>
          <?php
          $sch_key   = $sch_cursor->format('Y-m-d');
          $sch_slots = $sch_by_day[$sch_key] ?? [];
          $sch_out   = $sch_cursor->format('m') !== $sch_date->format('m');
          ?>
          <div class="schedule-month__day<?= $sch_out ? ' is-outside' : '' ?><?= $sch_key === $sch_today->format('Y-m-d') ? ' is-today' : '' ?>">
            <a class="schedule-month__date" href="<?= $sch_url(['view' => 'day', 'date' => $sch_key]) ?>">
              <?= $sch_cursor->format('j') ?>
            </a>
            <?php if ($sch_slots): ?>
              <ul class="schedule-month__slots">
                <?php foreach ($sch_slots as $sch_slot): ?>
                  <li>
                    <button type="button" class="schedule-month__slot" data-modal="booking?event=<?= $sch_slot['id'] ?>">
                      <span class="schedule-month__time"><?= $sch_slot['time'] ?></span>
                      <span class="schedule-month__name"><?= htmlspecialchars($sch_slot['title']) ?></span>
                    </button>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <?php $sch_cursor = $sch_cursor->modify('+1 day'); ?>
        <?php endwhile; ?>
      </div>

      <?php /* Мобільний варіант місяця: лише дні, де є заняття */ ?>
      <div class="schedule__list" data-reveal>
        <?php foreach ($sch_by_day as $sch_day => $sch_slots): ?>
          <?php $sch_d = new DateTimeImmutable($sch_day, $sch_tz); ?>
          <h3 class="schedule__day-head"><?= $sch_fmt('EEEE, d MMMM', $sch_d) ?></h3>
          <ul class="rows">
            <?php foreach ($sch_slots as $sch_slot): ?>
              <?php include __DIR__ . '/schedule-slot.php'; ?>
            <?php endforeach; ?>
          </ul>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <?php /* День і тиждень — той самий список .rows__item--slot, що вже
               є в проєкті й уже адаптивний. Різниця лише в кількості днів. */ ?>
      <div class="schedule__list" data-reveal>
        <?php
        $sch_cursor = $sch_from;
        while ($sch_cursor <= $sch_to):
          $sch_key   = $sch_cursor->format('Y-m-d');
          $sch_slots = $sch_by_day[$sch_key] ?? [];
          if (!$sch_slots) { $sch_cursor = $sch_cursor->modify('+1 day'); continue; }
        ?>
          <h3 class="schedule__day-head<?= $sch_key === $sch_today->format('Y-m-d') ? ' is-today' : '' ?>">
            <?= $sch_fmt($sch_view === 'day' ? 'EEEE, d MMMM' : 'EEEE, d MMM', $sch_cursor) ?>
          </h3>
          <ul class="rows">
            <?php foreach ($sch_slots as $sch_slot): ?>
              <?php include __DIR__ . '/schedule-slot.php'; ?>
            <?php endforeach; ?>
          </ul>
        <?php $sch_cursor = $sch_cursor->modify('+1 day'); endwhile; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php
unset($schedule_trainer, $schedule_direction, $schedule_location, $schedule_filters,
      $schedule_title, $schedule_link, $schedule_class, $schedule_id,
      $sch_tz, $sch_tz_name, $sch_today, $sch_view, $sch_date, $sch_from, $sch_to, $sch_data,
      $sch_by_day, $sch_refs, $sch_fmt, $sch_url, $sch_step, $sch_prev, $sch_next,
      $sch_caption, $sch_views, $sch_cursor, $sch_key, $sch_slots, $sch_slot,
      $sch_day, $sch_d, $sch_out, $sch_wd, $sch_hid, $sch_hname, $sch_aid,
      $sch_aname, $sch_iid, $sch_iname, $sch_k, $sch_label);
