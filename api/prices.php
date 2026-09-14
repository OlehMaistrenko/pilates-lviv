<?php
/**
 * Ціни (тарифи) — прокладка між сайтом і InstaSport API.
 *
 * Той самий принцип, що в api/schedule.php: реальний X-API-Key ніколи не
 * йде в браузер, тож запит роблять тільки звідси.
 *
 * Віддає JSON: {count, results: [{id, slug, title, items: [...]}]} —
 * уже згруповане по таб-групах і відсортоване, розмітці лишається просто
 * пройтись по масиву.
 *
 * Підключається і як HTTP-ендпоінт (AJAX/прямий виклик), і як бібліотека:
 * prices.php робить require і кличе prices_query() напряму.
 */

// Реальний ключ і clubslug — поза git (api/config.local.php у .gitignore).
// Поки файлу немає, працює мок.
$__cfg = __DIR__ . '/config.local.php';
$INSTASPORT = file_exists($__cfg) ? require $__cfg : null;

/**
 * Пагінований GET до InstaSport. Обидва ендпоінти цін — списки без
 * фільтрів, тому один спільний хелпер замість дублювання curl-блоку.
 */
function instasport_get_all(string $path): array {
  global $INSTASPORT;

  $url = rtrim($INSTASPORT['base'], '/') . $path . '?' . http_build_query(['page_size' => 200]);
  $all = [];
  while ($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 10,
      CURLOPT_HTTPHEADER     => ['X-API-Key: ' . $INSTASPORT['key']],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($body === false || $code !== 200) break;

    $page = json_decode($body, true);
    $all  = array_merge($all, $page['results'] ?? []);
    $url  = $page['next'] ?? null;
  }
  return $all;
}

/** GET /public/card_template/ — усі тарифи клубу. */
function fetch_card_templates(): array {
  global $INSTASPORT;
  // 'key', не сам масив: config.local.php тепер несе й інші секрети
  // (напр. mapbox) — файл існує й без InstaSport-ключа.
  if (!empty($INSTASPORT['key'])) return instasport_get_all('/public/card_template/');

  $mock = json_decode(file_get_contents(__DIR__ . '/prices-mock.json'), true);
  return $mock['results'];
}

/** GET /public/card_template_group/ — групи (таби) тарифів. */
function fetch_card_template_groups(): array {
  global $INSTASPORT;
  if (!empty($INSTASPORT['key'])) return instasport_get_all('/public/card_template_group/');

  $mock = json_decode(file_get_contents(__DIR__ . '/prices-mock.json'), true);
  return $mock['_groups'];
}

/**
 * Українська плюралізація: 1 день / 3 дні / 7 днів. Потрібна, бо кількість
 * відвідувань/днів/заморозок на картці — реальне число з API, не завжди 1 чи "багато".
 */
function plural_uk(int $n, string $one, string $few, string $many): string {
  $n = abs($n);
  $mod10 = $n % 10;
  $mod100 = $n % 100;
  if ($mod10 === 1 && $mod100 !== 11) return $one;
  if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14)) return $few;
  return $many;
}

/**
 * Тариф API → рядки для картки. Рядок додається, лише коли значення є і
 * ненульове — тариф без заморозок коротший за тариф із ними (скріншот
 * eталонної сторінки), це список наявних умов, а не таблиця з порожніми
 * клітинками. amount == -1 — безліміт (docs/ACCOUNT-WP.md §1.3), не
 * «-1 відвідування».
 */
function tariff_rows(array $t): array {
  $rows = [];

  $amount = (int)($t['amount'] ?? 0);
  if ($amount === -1) {
    $rows[] = ['КІЛЬКІСТЬ', 'Без обмежень'];
  } elseif ($amount > 0) {
    $rows[] = ['КІЛЬКІСТЬ', $amount . ' ' . plural_uk($amount, 'відвідування', 'відвідування', 'відвідувань')];
  }

  if (($d = (int)($t['duration'] ?? 0)) > 0) {
    $rows[] = ['ТЕРМІН ДІЇ', $d . ' ' . plural_uk($d, 'день', 'дні', 'днів')];
  }
  if (($p = (int)($t['pauses'] ?? 0)) > 0) {
    $rows[] = ['КІЛЬКІСТЬ ЗАМОРОЗОК', $p . ' ' . plural_uk($p, 'заморожування', 'заморожування', 'заморожувань')];
  }
  if (($pd = (int)($t['pause_duration'] ?? 0)) > 0) {
    $rows[] = ['ТРИВАЛІСТЬ ЗАМОРОЗКИ', $pd . ' ' . plural_uk($pd, 'день', 'дні', 'днів')];
  }
  if (($tr = (int)($t['transfer'] ?? 0)) > 0) {
    $rows[] = ['ПЕРЕНЕСЕННЯ', $tr . ' ' . plural_uk($tr, 'заняття', 'заняття', 'занять')];
  }

  return [
    'id'       => $t['id'],
    'title'    => $t['title'],
    'subtitle' => $t['subtitle'] ?? '',
    'price'    => $t['price'] ?? null,
    'rows'     => $rows,
  ];
}

/**
 * Головна точка входу: групи-таби (лише активні, status==1, непорожні) з
 * тарифами всередині, усе відсортоване за order (з id як тайбрейком —
 * usort нестабільний на дублях order).
 */
function prices_query(): array {
  $order_key = fn($x) => [$x['order'] ?? 0, $x['id']];

  $groups = array_values(array_filter(
    fetch_card_template_groups(),
    fn($g) => (int)($g['status'] ?? 0) === 1   // 2=сховані, 3=архів
  ));
  usort($groups, fn($a, $b) => $order_key($a) <=> $order_key($b));

  $by_group = [];
  foreach (fetch_card_templates() as $t) {
    $by_group[(int)$t['group']][] = $t;
  }
  foreach ($by_group as &$list) {
    usort($list, fn($a, $b) => $order_key($a) <=> $order_key($b));
  }
  unset($list);

  $tabs = [];
  foreach ($groups as $g) {
    $items = $by_group[(int)$g['id']] ?? [];
    if (!$items) continue;   // порожній таб — клік у нікуди

    $tabs[] = [
      'id'    => (int)$g['id'],
      'slug'  => 'g' . $g['id'],
      'title' => $g['title'],
      'items' => array_map('tariff_rows', $items),
    ];
  }

  return ['count' => count($tabs), 'results' => $tabs];
}

// HTTP-режим: файл викликали напряму, а не через require зі сторінки.
// Порівнюємо повні шляхи, а не basename: сторінка цін теж зветься
// prices.php, і по імені файлу прокладка вважала б себе ендпоінтом —
// вивалюючи JSON усередину сторінки.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(prices_query(), JSON_UNESCAPED_UNICODE);
}
