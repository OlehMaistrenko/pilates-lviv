<?php
/**
 * Самоперевірка прокладки розкладу: php api/schedule-test.php
 *
 * Прикриває саме те, чого не видно з розмітки: математику дат (діапазон,
 * сортування, DST), join zone → hall (у події немає ні назви, ні локації)
 * і три фільтри, яких публічний API InstaSport не вміє й які тому
 * рахуються в PHP — по тренеру, напрямку й локації.
 *
 * Числа нижче прив'язані до api/schedule-mock.json. Перегенеруєте мок —
 * перерахуйте очікування.
 */
require __DIR__ . '/schedule.php';

$fail = 0;
$chk = function (string $label, bool $cond, string $extra = '') use (&$fail): void {
  if (!$cond) { $fail++; echo "FAIL  $label  $extra\n"; }
  else        { echo "ok    $label  $extra\n"; }
};

$RANGE = ['start_date' => '2026-09-01', 'end_date' => '2026-10-31'];

/* ---- Діапазон і порядок ---- */
$week  = schedule_query(['start_date' => '2026-09-07', 'end_date' => '2026-09-13']);
$dates = array_column($week['results'], 'date');
$chk('діапазон тижня включно', min($dates) >= '2026-09-07' && max($dates) <= '2026-09-13',
     min($dates) . '..' . max($dates) . ' n=' . $week['count']);
$chk('відсортовано за часом',
     $week['results'][0]['timestamp'] <= end($week['results'])['timestamp']);
$chk('неділя порожня', !in_array('2026-09-13', $dates, true));

/* ---- Денормалізація: в Event немає ні назви, ні локації ---- */
$s = $week['results'][0];
$chk('назва з template',        $s['title'] !== '', $s['title']);
$chk('локація через zone→hall', $s['location'] !== '', 'hall=' . $s['hall'] . ' ' . $s['location']);
$chk('напрямок з activity',     $s['direction'] !== '', $s['direction']);
$chk('тренер',                  !empty($s['trainers'][0]['name']), $s['trainers'][0]['name']);
$chk('формат часу',             (bool)preg_match('/^\d{2}:\d{2}$/', $s['time']), $s['time']);

/* ---- Фільтри, яких немає в публічному API ----
   Оксана (id 4) — єдина в моку, хто веде у двох локаціях: на ній видно,
   що комбінований фільтр справді звужує, а не збігається випадково. */
$oks   = schedule_query($RANGE + ['trainer' => 4]);
$oksBr = schedule_query($RANGE + ['trainer' => 4, 'location' => 2]);
$oksSy = schedule_query($RANGE + ['trainer' => 4, 'location' => 3]);
$chk('фільтр по тренеру',      $oks['count'] === 27, 'n=' . $oks['count']);
$chk('тренер + локація',       $oksBr['count'] === 18 && $oksSy['count'] === 9,
     'Брюховичі=' . $oksBr['count'] . ' Сихів=' . $oksSy['count']);
$chk('частини = ціле',         $oksBr['count'] + $oksSy['count'] === $oks['count']);

$hal   = schedule_query($RANGE + ['trainer' => 3]);
$halRf = schedule_query($RANGE + ['trainer' => 3, 'direction' => 2]);
$chk('тренер + напрямок',      $halRf['count'] > 0 && $halRf['count'] < $hal['count'],
     $halRf['count'] . ' < ' . $hal['count']);

$loc = schedule_query($RANGE + ['location' => 2]);
$chk('фільтр по локації',      count(array_unique(array_column($loc['results'], 'location'))) === 1,
     'n=' . $loc['count']);
$dir = schedule_query($RANGE + ['direction' => 2]);
$chk('фільтр по напрямку',     count(array_unique(array_column($dir['results'], 'direction'))) === 1,
     'n=' . $dir['count']);

/* ---- Межові випадки: сміття в URL не має валити сторінку ---- */
$chk('порожній перетин',   schedule_query($RANGE + ['trainer' => 5, 'location' => 3])['count'] === 0);
$chk('невідомий тренер',   schedule_query($RANGE + ['trainer' => 999])['count'] === 0);
$chk('сміття в датах',     is_array(schedule_query(['start_date' => 'не-дата', 'end_date' => '2026-13-45'])['results']));
$chk('end < start',        schedule_query(['start_date' => '2026-09-10', 'end_date' => '2026-09-01'])['count'] >= 0);

/* ---- DST: Київ переходить на зимовий час 25 жовтня 2026 ---- */
$oct = schedule_query(['start_date' => '2026-10-31', 'end_date' => '2026-10-31']);
$chk('заняття після переводу годинника', $oct['count'] > 0, 'n=' . $oct['count']);

echo $fail ? "\n$fail FAILED\n" : "\nусе пройшло\n";
exit($fail ? 1 : 0);
