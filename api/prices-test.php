<?php
/**
 * Самоперевірка prices_query() на фікстурі api/prices-mock.json.
 * Запуск: php api/prices-test.php
 */
require __DIR__ . '/prices.php';

$failed = 0;
$chk = function (string $name, bool $ok) use (&$failed) {
  echo ($ok ? "OK   " : "FAIL ") . $name . "\n";
  if (!$ok) $failed++;
};

$data = prices_query();
$slugs = array_column($data['results'], 'slug');

$chk('архівна група (status=3) не в табах', !in_array('g9', $slugs, true));
$chk('порожня група (status=1, без тарифів) не в табах', !in_array('g5', $slugs, true));
$chk('таби відсортовані за order', $slugs === ['g1', 'g2', 'g3', 'g4']);

$g1 = $data['results'][0];
$chk('картки групи 1 відсортовані за order', array_column($g1['items'], 'id') === [1, 2, 3]);

$noFreeze = $g1['items'][0];   // id 1: pauses=0, pause_duration=0, transfer=0
$withFreeze = $g1['items'][1]; // id 2: pauses=1, pause_duration=7, transfer=1
$chk('тариф без заморозок коротший (2 рядки)', count($noFreeze['rows']) === 2);
$chk('тариф із заморозками й перенесенням довший (5 рядків)', count($withFreeze['rows']) === 5);

$hasZeroValue = false;
foreach ($noFreeze['rows'] as [$label, $value]) {
  if (preg_match('/(^|\s)0(\s|$)/u', $value)) $hasZeroValue = true;
}
$chk('немає рядків з нульовим значенням', !$hasZeroValue);

$g2 = $data['results'][1];
$unlimited = $g2['items'][2]; // id 6: amount=-1
$unlimitedRow = $unlimited['rows'][0];
$chk('amount=-1 рендериться як "Без обмежень"', $unlimitedRow === ['КІЛЬКІСТЬ', 'Без обмежень']);

$chk('plural_uk: 1 день', plural_uk(1, 'день', 'дні', 'днів') === 'день');
$chk('plural_uk: 3 дні', plural_uk(3, 'день', 'дні', 'днів') === 'дні');
$chk('plural_uk: 7 днів', plural_uk(7, 'день', 'дні', 'днів') === 'днів');
$chk('plural_uk: 11 днів (виняток -надцять)', plural_uk(11, 'день', 'дні', 'днів') === 'днів');
$chk('plural_uk: 21 день (виняток після 11-14)', plural_uk(21, 'день', 'дні', 'днів') === 'день');

echo $failed ? "\n{$failed} перевірок провалено\n" : "\nусе пройшло\n";
exit($failed ? 1 : 0);
