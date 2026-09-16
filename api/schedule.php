<?php
/**
 * Розклад — прокладка між сайтом і InstaSport API.
 *
 * Єдине місце, що знає про InstaSport. Причина, чому запит не йде з браузера:
 * бойовий X-API-Key прив'язаний до домену та IP, тож fetch зі сторінки і
 * ключ засвітив би, і не пройшов би прив'язку.
 *
 * GET-параметри (усе опційне):
 *   start_date, end_date — YYYY-MM-DD, обидві межі включно (як в API)
 *   trainer   — id інструктора
 *   direction — id активності (напрямку)
 *   location  — id локації (hall)
 *
 * Віддає JSON у формі public/event/: {count, next, previous, results[]},
 * але з денормалізованими полями (див. denormalize нижче).
 *
 * Підключається і як HTTP-ендпоінт (AJAX), і як бібліотека:
 * partials/schedule.php робить require і кличе schedule_query() напряму,
 * щоб не ходити по HTTP сам до себе на серверному рендері. Так само
 * partials/modals/slot-details.php кличе fetch_event_by_id() +
 * denormalize() напряму — точковий запит за id одного заняття, не через
 * schedule_query().
 */

// Реальний ключ і clubslug — поза git (api/config.local.php у .gitignore).
// Поки файлу немає, працює мок.
$__cfg = __DIR__ . '/config.local.php';
$INSTASPORT = file_exists($__cfg) ? require $__cfg : null;

/**
 * Єдина функція, що ходить по дані. Перемикання на живий API — заміна
 * рівно цього тіла, решта файлу не змінюється.
 */
function fetch_events(string $from, string $to, ?int $hall = null): array {
  global $INSTASPORT;

  // 'key', не сам масив: config.local.php тепер несе й інші секрети
  // (напр. mapbox) — файл існує й без InstaSport-ключа.
  if (!empty($INSTASPORT['key'])) {
    // Живий API. Пагінація обов'язкова: page_size стелить видачу, а
    // діапазон у два місяці легко перевищує одну сторінку.
    $url = rtrim($INSTASPORT['base'], '/') . "/public/event/?" . http_build_query(array_filter([
      'start_date' => $from,
      'end_date'   => $to,
      'hall'       => $hall,
      'page_size'  => 200,
    ], fn($v) => $v !== null));

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
      $all = array_merge($all, $page['results'] ?? []);
      $url = $page['next'] ?? null;
    }
    return ['results' => $all, 'refs' => instasport_refs()];
  }

  // Мок тієї самої форми. Довідники лежать поруч у _refs — у реальному API
  // це окремі ендпоінти (public/activity/, public/hall/, public/hall_zone/).
  $mock = json_decode(file_get_contents(__DIR__ . '/schedule-mock.json'), true);

  // Діапазон фільтруємо тут: у живого API це роблять start_date/end_date,
  // і мок мусить поводитись так само, інакше тиждень показав би два місяці.
  $results = array_values(array_filter(
    $mock['results'],
    fn($e) => ($d = substr($e['date'], 0, 10)) >= $from && $d <= $to
  ));

  return ['results' => $results, 'refs' => $mock['_refs']];
}

/**
 * Одне заняття за id — /public/event/{id}/, детальний ендпоінт (не
 * список): модалка деталей питає точково, а не шукає по вже
 * завантаженому діапазону дат.
 */
function fetch_event_by_id(int $id): ?array {
  global $INSTASPORT;

  if (!empty($INSTASPORT['key'])) {
    $ch = curl_init(rtrim($INSTASPORT['base'], '/') . "/public/event/{$id}/");
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 10,
      CURLOPT_HTTPHEADER     => ['X-API-Key: ' . $INSTASPORT['key']],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($body === false || $code !== 200) return null;
    return ['event' => json_decode($body, true), 'refs' => instasport_refs()];
  }

  $mock = json_decode(file_get_contents(__DIR__ . '/schedule-mock.json'), true);
  $event = null;
  foreach ($mock['results'] as $e) {
    if ($e['id'] === $id) { $event = $e; break; }
  }
  return $event ? ['event' => $event, 'refs' => $mock['_refs']] : null;
}

/** Довідники живого API — окремі ендпоінти, кешовані на час запиту. */
function instasport_refs(): array {
  global $INSTASPORT;
  static $refs = null;
  if ($refs !== null) return $refs;

  $get = function (string $path) use ($INSTASPORT): array {
    $ch = curl_init(rtrim($INSTASPORT['base'], '/') . $path);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 10,
      CURLOPT_HTTPHEADER     => ['X-API-Key: ' . $INSTASPORT['key']],
    ]);
    $body = curl_exec($ch);
    curl_close($ch);
    $data = json_decode((string)$body, true);
    return $data['results'] ?? [];
  };

  $activities = $instructors = $halls = $zones = [];
  foreach ($get('/public/activity/') as $a)   $activities[$a['id']] = $a['title'];
  foreach ($get('/public/instructor/') as $i) $instructors[$i['id']] = $i['name'];
  foreach ($get('/public/hall/') as $h)       $halls[$h['id']] = $h['title'];
  foreach ($get('/public/hall_zone/') as $z)  $zones[$z['id']] = $z['hall'];

  return $refs = compact('activities', 'instructors', 'halls', 'zones');
}

/**
 * Подія InstaSport → плаский слот для розмітки.
 *
 * Потрібно, бо в Event немає ні назви, ні локації: назва/тривалість/місця
 * лежать у template, activity — голий id, а локація дістається лише
 * ланцюжком template.zone → hall_zone.hall.
 */
function denormalize(array $e, array $refs, DateTimeZone $tz): array {
  $tpl      = $e['template'] ?? [];
  $activity = $tpl['activity'] ?? null;
  $zone     = $tpl['zone'] ?? null;
  $hall     = $zone !== null ? ($refs['zones'][$zone] ?? null) : null;

  // API віддає date-time без задокументованої зони, а сервер за
  // замовчуванням в UTC — без явного переводу заняття о 09:00 з'їхало б.
  // setTimezone, а не $tz у конструкторі: коли рядок несе власний зсув
  // (+03:00), конструктор другий аргумент ігнорує, і переводить саме воно.
  $start = (new DateTimeImmutable($e['date']))->setTimezone($tz);
  $seats = $e['seats'] ?? ($tpl['seats'] ?? null);

  // enroll_deadline — межа, після якої запис закритий незалежно від
  // місць (є і в списковому, і в детальному ендпоінті живого API). Мок
  // цього поля не має — фолбек timestamp<now, той самий сенс (заняття
  // вже почалось/минуло), на полі, яке є завжди.
  $enroll_deadline = isset($e['enroll_deadline'])
    ? (new DateTimeImmutable($e['enroll_deadline']))->getTimestamp()
    : null;
  $is_past = $enroll_deadline !== null
    ? time() > $enroll_deadline
    : $start->getTimestamp() < time();

  return [
    'id'          => $e['id'],
    'date'        => $start->format('Y-m-d'),
    'time'        => $start->format('H:i'),
    'timestamp'   => $start->getTimestamp(),
    'title'       => $tpl['title'] ?? '',
    'duration'    => $tpl['duration'] ?? '',
    'seats'       => $seats,
    'price'       => $e['price'] ?? ($tpl['price'] ?? null),
    'is_bookable' => $seats !== 0 && !$is_past,
    'variant'     => $tpl['variant'] ?? null,
    // Якими шаблонами абонементів можна оплатити це заняття — вхід фільтра
    // способів оплати (docs/ACCOUNT-WP.md §1.2). Мок поля не має, тож
    // фолбек — порожній масив: «обмежень не знаємо», фільтр їх не накладає.
    'template_id'     => $tpl['id'] ?? null,
    'card_templates'  => $tpl['card_templates'] ?? [],
    'activity'    => $activity,
    'direction'   => $activity !== null ? ($refs['activities'][$activity] ?? '') : '',
    'hall'        => $hall,
    'location'    => $hall !== null ? ($refs['halls'][$hall] ?? '') : '',
    'trainers'    => array_map(
      fn($i) => ['id' => $i['id'], 'name' => $i['name'] ?? ($refs['instructors'][$i['id']] ?? '')],
      $e['instructors'] ?? []
    ),
  ];
}

/**
 * Головна точка входу. Фільтри по тренеру й напрямку рахуються ТУТ, а не
 * параметрами запиту: публічний ендпоінт InstaSport уміє фільтрувати лише
 * по hall/zone/датах, instructor є тільки в manager-версії.
 */
function schedule_query(array $params): array {
  $tz = new DateTimeZone('Europe/Kyiv');

  $today = (new DateTimeImmutable('now', $tz))->format('Y-m-d');
  $from  = valid_date($params['start_date'] ?? '') ?? $today;
  $to    = valid_date($params['end_date'] ?? '')   ?? $from;
  if ($to < $from) $to = $from;

  $trainer   = isset($params['trainer'])   && $params['trainer']   !== '' ? (int)$params['trainer']   : null;
  $direction = isset($params['direction']) && $params['direction'] !== '' ? (int)$params['direction'] : null;
  $location  = isset($params['location'])  && $params['location']  !== '' ? (int)$params['location']  : null;

  ['results' => $raw, 'refs' => $refs] = fetch_events($from, $to, $location);

  $slots = [];
  foreach ($raw as $e) {
    $slot = denormalize($e, $refs, $tz);

    if ($location  !== null && $slot['hall'] !== $location) continue;
    if ($direction !== null && $slot['activity'] !== $direction) continue;
    if ($trainer   !== null) {
      $ids = array_column($slot['trainers'], 'id');
      if (!in_array($trainer, $ids, true)) continue;
    }
    $slots[] = $slot;
  }

  usort($slots, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);

  return [
    'count'    => count($slots),
    'next'     => null,
    'previous' => null,
    'results'  => $slots,
    'refs'     => $refs,
  ];
}

/** YYYY-MM-DD або null — сміття в URL не має валити сторінку. */
function valid_date(string $s): ?string {
  $d = DateTimeImmutable::createFromFormat('!Y-m-d', $s);
  return ($d && $d->format('Y-m-d') === $s) ? $s : null;
}

/**
 * Назва зони для IntlDateFormatter. Europe/Kyiv — сучасна назва, але в
 * старіших збірках ICU (яку тягне intl) її ще немає, і форматер падає з
 * U_ILLEGAL_ARGUMENT_ERROR, хоча сам PHP таку зону приймає (бази даних
 * дві й вони розходяться: MAMP несе ICU 56, де є лише Europe/Kiev).
 * Пробуємо тим самим викликом, що й бойовий — з патерном: коротшу форму
 * конструктора старий ICU ковтає без помилки.
 */
function icu_tz_name(): string {
  static $name = null;
  if ($name !== null) return $name;

  $name = 'Europe/Kyiv';
  try {
    new IntlDateFormatter('uk_UA', IntlDateFormatter::NONE, IntlDateFormatter::NONE, $name, null, 'd');
  } catch (Throwable) {
    $name = 'Europe/Kiev';
  }
  return $name;
}

// HTTP-режим: файл викликали напряму, а не через require з партіалу.
// Порівнюємо повні шляхи, а не basename: сторінка розкладу теж зветься
// schedule.php, і по імені файлу прокладка вважала б себе ендпоінтом —
// вивалюючи JSON усередину сторінки.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(schedule_query($_GET), JSON_UNESCAPED_UNICODE);
}
