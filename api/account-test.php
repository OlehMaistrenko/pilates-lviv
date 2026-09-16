<?php
/**
 * Самоперевірка логіки кабінету на фікстурі api/account-mock.json.
 * Запуск: php api/account-test.php
 *
 * Числа прив'язані до моку — міняєш фікстуру, правиш і очікування.
 */
require __DIR__ . '/account.php';

$failed = 0;
$chk = function (string $name, bool $ok) use (&$failed) {
  echo ($ok ? "OK   " : "FAIL ") . $name . "\n";
  if (!$ok) $failed++;
};

/* ---- Профіль (сімейні профілі не реалізуються — тестуємо лише основний) ---- */
$profile = account_profile();
$chk('основний профіль — relation == 1', (int)$profile['relation'] === 1);
$chk('основний профіль — Ірина', $profile['name'] === 'Ірина Ковальчук');

/* ---- Абонементи: дві шкали статусів не плутаються ---- */
$active  = account_cards(1);
$archive = account_cards(3);
$chk('активні абонементи: 3 (заявка + 2 активні)', count($active) === 3);
$chk('архів: 2 (по відвідуваннях + по строку)', count($archive) === 2);
$chk('жоден абонемент не потрапив в обидві вкладки',
  !array_intersect(array_column($active, 'id'), array_column($archive, 'id')));
$chk('заявка (status=1) — серед активних, не в архіві',
  in_array(57, array_column($active, 'id'), true));

/* ---- amount == -1 — безліміт, а не «-1 заняття» (§1.3) ---- */
$unlimited = null;
$limited   = null;
foreach ($active as $c) {
  if ((int)$c['amount'] === -1) $unlimited = $c;
  if ((int)$c['id'] === 45)     $limited   = $c;
}
$chk('безлімітний: card_left() === null', card_left($unlimited) === null);
$chk('безлімітний завжди має що витрачати', card_has_left($unlimited) === true);
$chk('абонемент 10 занять, використано 4 → лишилось 6', card_left($limited) === 6);

$spent = null;
foreach ($archive as $c) if ((int)$c['id'] === 52) $spent = $c;
$chk('вичерпаний (8 з 8) → лишилось 0', card_left($spent) === 0);
$chk('вичерпаний не має що витрачати', card_has_left($spent) === false);

/* ---- status_detail / paid_detail — готові рядки з API, не наші ---- */
$chk('status_detail береться з API як є', $limited['status_detail'] === 'Активний');
$chk('paid_detail береться з API як є', $limited['paid_detail'] === 'Liqpay');

/* ---- Візити: майбутні vs минулі ---- */
$upcoming = account_visits('upcoming');
$past     = account_visits('past');
$all      = account_visits();
$chk('усі візити: 6', count($all) === 6);
$chk('майбутні + минулі = усі', count($upcoming) + count($past) === count($all));
$chk('скасований візит не вважається майбутнім',
  !in_array(9254, array_column($upcoming, 'id'), true));
$chk('візити відсортовані найновішими вгору',
  array_column($all, 'id') === [9302, 9301, 9288, 9276, 9261, 9254]);

$chk('минулий візит не скасовується', visit_is_cancellable($past[0]) === false);

/* ---- Способи оплати (§1.2, §10) ---- */
// Заняття, оплатити яке можна шаблонами 2 і 6 — це абонементи 45 і 51.
$event = ['price' => '350.00', 'card_templates' => [2, 6]];
$opts  = booking_payment_options($event);
$cards = array_values(array_filter($opts, fn($o) => $o['type'] === 'card'));
$chk('абонементи відфільтровані по card_templates: 2', count($cards) === 2);
$chk('абонемент іншого напрямку (шаблон 3) не пропонується',
  !in_array(52, array_column($cards, 'card'), true));
$chk('заявка (не активний) не пропонується як оплата',
  !in_array(57, array_column($cards, 'card'), true));

// Заняття лише під шаблон 4 — жоден активний абонемент не підходить.
$other = booking_payment_options(['price' => '900.00', 'card_templates' => [4]]);
$chk('немає відповідного абонемента → жодного варіанта "card"',
  !array_filter($other, fn($o) => $o['type'] === 'card'));

$types = array_column($opts, 'type');
$chk('рахунок пропонується, коли на ньому вистачає', in_array('account', $types, true));
$chk('рахунок НЕ пропонується, коли не вистачає (1200 < 9000)',
  !in_array('account', array_column(booking_payment_options(
    ['price' => '9000.00', 'card_templates' => []]
  ), 'type'), true));
$chk('картка і бронь є завжди',
  in_array('online', $types, true) && in_array('request', $types, true));
$chk('порожній card_templates не ріже абонементи',
  count(array_filter(booking_payment_options(
    ['price' => '350.00', 'card_templates' => []]
  ), fn($o) => $o['type'] === 'card')) === 2);

/* ---- Форматери ---- */
$nb = "\u{00A0}";   // нерозривний пробіл, яким склеєні розряд і «грн»
$chk('ціна без копійок', format_price('1200.00') === "1{$nb}200{$nb}грн");
$chk('ціна з копійками', format_price('350.50') === "350,50{$nb}грн");
$chk('коротка дата', format_date_short('2026-09-30') === '30.09');
$chk('довга дата без часу', format_date_long('2026-09-18T09:30:00+03:00', false) === '18 вересня');
$chk('порожня дата не падає', format_date_long(null) === '');
$chk('битий рядок дати не падає', format_date_long('не дата') === '');

// Час показуємо в зоні студії, а не в зоні сервера (за замовчуванням UTC):
// інакше заняття о 09:30 клієнт побачив би о 06:30.
$chk('час у зоні студії, не UTC',
  format_date_long('2026-09-18T09:30:00+03:00') === '18 вересня, 09:30');
$chk('дата в UTC переводиться в київську',
  format_date_long('2026-09-18T06:30:00+00:00') === '18 вересня, 09:30');

// Слот із denormalize уже в зоні студії й розкладений на date+time без
// зсуву — формувати його через format_date_long означало б перевести час
// удруге (10:00 → 13:00). Тому окремий format_slot_datetime().
$chk('слот розкладу не переводиться в зону вдруге',
  format_slot_datetime(['date' => '2026-09-17', 'time' => '10:00']) === '17 вересня, 10:00');
$chk('слот без часу — лише дата',
  format_slot_datetime(['date' => '2026-09-17', 'time' => '']) === '17 вересня');

/* ---- /user/ — особисті дані (PATCH /user/), окремо від /profile/info/ ---- */
$user = account_user();
$chk('account_user() читає _user, а не profile', $user['first_name'] === 'Ірина');
$chk('gender — число з GenderEnum', $user['gender'] === 2);

$genders = account_genders();
$chk('GenderEnum: 0/1/2 з підписами', $genders === [0 => 'Не вказувати', 1 => 'Чоловіча', 2 => 'Жіноча']);

$limits = account_user_limits();
$chk('maxLength зі схеми User: first_name/last_name 20', $limits['first_name'] === 20 && $limits['last_name'] === 20);
// about/instagram прибрані з форми (account.php) — лімітів для них тут
// немає навмисно, перевіряти нічого.

echo $failed ? "\n{$failed} перевірок провалено\n" : "\nусе пройшло\n";
exit($failed ? 1 : 0);
