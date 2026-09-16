<?php
/**
 * Кабінет клієнта — мапінг відповідей InstaSport у розмітку.
 *
 * Це шар 2 за docs/ACCOUNT-WP.md §6.1: чиста логіка, яка переїде на WP
 * без змін. Тому тут НЕМАЄ ні curl_*, ні $_SESSION, ні $_COOKIE — коли
 * зʼявиться бойовий ключ, HTTP і сесія прийдуть із api/instasport.php та
 * api/session.php (кроки 1–2 ТЗ), а функції нижче лишаться як є.
 *
 * Поки ключа немає — читаємо api/account-mock.json, тим самим прийомом,
 * що api/schedule.php і api/prices.php.
 *
 * ============================================================
 * ДОВІДКА ПО ЕНДПОІНТАХ (звірено зі схемою instasport.ua/doc/api/schema/,
 * OpenAPI 3.0.3). Записано тут, щоб на етапі бекенду не досліджувати
 * схему вдруге. База: /admin/club/{clubslug}/api/v2
 *
 * --- Авторизація (X-API-Key, без токена) ---
 * Обрано ТЕЛЕФОН, а не email: у phone-флоу кожен крок — задокументований
 * ендпоінт з явним кодом підтвердження, який клієнт вводить у формі, і ми
 * самі керуємо всім циклом. Email-аналоги (email_login_signup/
 * email_reset_password) підтверджуються ЛИСТОМ ІЗ ПОСИЛАННЯМ, чийого
 * формату схема не описує (немає email_verify/email_reset_password_verify) —
 * зробити цей флоу надійно зараз не можна, тому свідомо не використовуємо
 * його як спосіб входу. Email лишається в профілі як довідковий контакт
 * (account_user(), не автентифікація) і має власну зміну — /user/email_update/
 * нижче.
 *
 * POST /auth/phone_login_signup/  {phone*, password?}
 *      200 → {token, refresh}: клієнт існує, пароль зійшовся
 *      201 → порожньо: новий клієнт створений, SMS з кодом пішла
 *      400 → невірний формат телефону / відсутнє поле
 *      401 → заблокований (is_active=False) / невірний пароль
 *      409 → integrity_error, колізія при створенні
 * POST /auth/phone_verify/  {phone*, code*, password?} → 200 {token, refresh}
 *      401 → невірний телефон/код, заблокований, або помилка токена
 * POST /auth/phone_reset_password/        {phone*} → 200, SMS; 400 — телефон
 *      не зареєстрований / помилка SMS
 * POST /auth/phone_reset_password_verify/ {phone*, code*, password*}
 *      → 200 {token, refresh}: пароль змінено, клієнт одразу залогінений
 *      400 → невірний код/формат/телефон не знайдено
 * POST /auth/refresh/  {refresh*} → 200 {token, refresh}; 403 invalid_refresh_token
 *
 * --- Особисті дані (Authorization: Bearer) ---
 * GET   /user/        → User {first_name, last_name, avatar, about,
 *                             birthday, gender, instagram}
 * PATCH /user/        ← PatchedUser, ті самі поля, усі опційні.
 *                       maxLength: first_name/last_name 20, about 150,
 *                       instagram 30. gender: 0 без статі · 1 чол · 2 жін.
 *                       200 → User; 400 wrong_parameters; 401 expired_token
 * DELETE /user/       → 204. Тільки якщо немає візитів/абонементів.
 * POST  /user/phone_update/        {phone*}             — надсилає SMS
 * POST  /user/phone_update_verify/ {code*}              — підтверджує номер
 * POST  /user/email_update/        {email*, next_url*}  — підтвердження
 *       листом. Той самий незʼясований формат посилання, що вище — email
 *       тут лише довідковий контакт, не критичний шлях. Деталі питання —
 *       docs/ACCOUNT-WP.md §15.8.
 *
 * ВАЖЛИВО: /profile/info/ — GET-only, усі поля readOnly (name, account,
 * relation_detail, rules_accepted). Редагування особистих даних іде через
 * /user/, а НЕ через /profile/info/. /profile/info/ — це запис клієнта в
 * клубі (у відповіді API це список: основний профіль + сімейні, але
 * сімейні профілі в проєкті свідомо не реалізуються); /user/ — сам
 * обліковий запис. Єдиний запис у /profile/: POST /profile/info/accept_rules/.
 *
 * --- Дані кабінету (Authorization: Bearer) ---
 * GET /profile/info/             профіль клієнта (основний запис зі списку)
 * GET /profile/card/?status=     абонементи. ШКАЛА ФІЛЬТРА: 1 активні ·
 *                                2 приховані · 3 архів — це НЕ ProfileCard.status
 * GET /profile/visit/            історія записів
 * GET /profile/account_deposit/  рух коштів (+ POST — поповнення)
 * POST   /profile/visit/         запис на тренування {event, payment_type, card?}
 * DELETE /profile/visit/{id}/    скасування
 * ============================================================
 */

$__cfg = __DIR__ . '/config.local.php';
$INSTASPORT = file_exists($__cfg) ? require $__cfg : null;

/**
 * Сире джерело даних кабінету. Єдине місце, яке знає, що зараз працює мок:
 * решта файлу вже оперує структурами InstaSport.
 */
function account_source(): array {
  static $data = null;
  if ($data === null) {
    $data = json_decode(file_get_contents(__DIR__ . '/account-mock.json'), true);
  }
  return $data;
}

/**
 * Чи залогінений клієнт. На моках керується ?guest у query — інакше
 * гостьові стани (модалка входу, booking для неавторизованого) не було б
 * як подивитись. Реальну перевірку дасть session.php: current_client() !== null.
 */
function account_is_logged(): bool {
  return !isset($_GET['guest']);
}

/**
 * GET /profile/info/ — профіль клієнта.
 *
 * У відповіді API це список (основний профіль + сімейні), але сімейні
 * профілі свідомо не реалізуються (не входять у v1), тож тут завжди
 * повертаємо перший запис — той, що з relation == 1 (основний), якщо він
 * є, інакше перший-ліпший.
 */
function account_profile(): ?array {
  $all = account_source()['_profile']['results'] ?? [];
  foreach ($all as $p) {
    if ((int)($p['relation'] ?? 0) === 1) return $p;
  }
  return $all[0] ?? null;
}

/**
 * GET /user/ — особисті дані облікового запису (схема User).
 *
 * Це НЕ /profile/info/: там профілі клієнта в клубі й усі поля readOnly,
 * а редаговані дані живуть саме тут. Поля: first_name, last_name, avatar,
 * about, birthday, gender, instagram.
 */
function account_user(): array {
  return account_source()['_user'] ?? [];
}

/**
 * Довідник статі — GenderEnum зі схеми (0/1/2). Тримаємо мапу тут, а не в
 * розмітці: значення числові, і підпис до них у формі має бути один.
 */
function account_genders(): array {
  return [0 => 'Не вказувати', 1 => 'Чоловіча', 2 => 'Жіноча'];
}

/**
 * Обмеження довжини полів із схеми User — розмітка ставить із них
 * maxlength, щоб браузер різав рядок ще до 400 wrong_parameters від API.
 *
 * Схема має ще about (150) й instagram (30) — форма їх свідомо не показує
 * (див. account.php), тож і лімітів для них тут немає; якщо поля повернуть
 * у форму, значення взяти з докблоку GET /user/ вище.
 */
function account_user_limits(): array {
  return ['first_name' => 20, 'last_name' => 20];
}

/**
 * Скільки занять лишилось на абонементі. amount == -1 — безліміт
 * (§1.3), і це не «-1 заняття»: повертаємо null, а розмітка малює «Без обмежень».
 */
function card_left(array $card): ?int {
  $amount = (int)($card['amount'] ?? 0);
  if ($amount === -1) return null;
  return max(0, $amount - (int)($card['used'] ?? 0));
}

/**
 * Чи абонемент іще можна витратити. Безлімітний — завжди так, решта —
 * поки лишились заняття. Використовується і в кабінеті, і у фільтрі
 * способів оплати (§1.2).
 */
function card_has_left(array $card): bool {
  $left = card_left($card);
  return $left === null || $left > 0;
}

/**
 * GET /profile/card/ — абонементи клієнта.
 *
 * $status — шкала ФІЛЬТРА InstaSport (1 активні · 2 приховані · 3 архів),
 * не ProfileCard.status. Це різні шкали, і плутанина між ними дала б
 * «активний» абонемент у вкладці архіву. Мок тримає повний список, тож
 * розкладаємо самі: активні — status 1/2/4/5 (заявка, продовження,
 * активний, заморожений), решта — архів.
 */
function account_cards(int $status = 1): array {
  $all = account_source()['_cards']['results'] ?? [];
  $active_statuses = [1, 2, 4, 5];

  return array_values(array_filter($all, function ($c) use ($status, $active_statuses) {
    $is_active = in_array((int)($c['status'] ?? 0), $active_statuses, true);
    return $status === 1 ? $is_active : !$is_active;
  }));
}

/**
 * GET /profile/visit/ — історія записів, найновіші зверху.
 * $when: 'upcoming' майбутні (їх можна скасувати), 'past' минулі, '' усі.
 */
function account_visits(string $when = ''): array {
  $all = account_source()['_visits']['results'] ?? [];
  $now = time();

  if ($when !== '') {
    $all = array_filter($all, function ($v) use ($when, $now) {
      $is_upcoming = strtotime($v['date']) >= $now
        && (int)($v['status'] ?? 0) === 1;   // 1 — заплановане; скасоване не «майбутнє»
      return $when === 'upcoming' ? $is_upcoming : !$is_upcoming;
    });
  }

  $all = array_values($all);
  usort($all, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));
  return $all;
}

/**
 * Чи можна скасувати запис. DELETE /profile/visit/{id}/ доступний, поки
 * не минув decline_deadline (§10); скільки разів — обмежує card.transfer
 * того абонемента, з якого списано заняття.
 *
 * TODO ПЕРЕВІРИТИ: $visit['status'] тут — ПОЛЕ, ЯКОГО НЕМАЄ В РЕАЛЬНІЙ
 * СХЕМІ ProfileVisit (перевірено безпосередньо зі схеми: id, profile,
 * event, event_date, event_duration, price, date_created, user_created,
 * date_authorized, user_authorized, authorized(bool), paid, paid_detail,
 * paid_by_card, options — status відсутній узагалі). На моках спрацьовує,
 * бо фікстура сама його додає, але на бойовому API це завжди null → 0 →
 * функція завжди повертає false, і кнопка «Скасувати» ніде не з'явиться.
 * Найближчий реальний кандидат — `authorized`: запис, який ще НЕ
 * authorized, і є той, що можна відкликати/скасувати до підтвердження;
 * але це припущення, не підтверджене документацією — уточнити на
 * тестовому акаунті (docs/ACCOUNT-WP.md §15.4), перш ніж міняти логіку.
 * decline_deadline теж не задокументований як поле ProfileVisit — саму
 * дату скасування, найімовірніше, дає event (enroll/decline на боці
 * заняття, як seats/enroll_deadline в api/schedule.php), а не сам visit.
 */
function visit_is_cancellable(array $visit): bool {
  if ((int)($visit['status'] ?? 0) !== 1) return false;
  $deadline = $visit['decline_deadline'] ?? null;
  return $deadline === null || time() < strtotime($deadline);
}

/** GET /profile/account_deposit/ — історія руху коштів, найновіші зверху. */
function account_deposits(): array {
  $all = account_source()['_deposits']['results'] ?? [];
  usort($all, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));
  return $all;
}

/**
 * Способи оплати конкретного заняття — рівно список із §10.
 *
 * Абонементи фільтруються за §1.2: активні ∩ дозволені для цього заняття
 * (card.template ∈ event.template.card_templates) ∩ ще є заняття. Без
 * цього фільтра клієнт побачив би свій абонемент на йогу як спосіб
 * оплатити реформер — і отримав 400 на сабміті.
 *
 * $event — слот із api/schedule.php (denormalize), не сира подія API.
 */
function booking_payment_options(array $event): array {
  $allowed = $event['card_templates'] ?? [];
  $options = [];

  foreach (account_cards(1) as $card) {
    if ((int)($card['status'] ?? 0) !== 4) continue;            // тільки активний, не заявка
    if ($allowed && !in_array((int)$card['template'], $allowed, true)) continue;
    if (!card_has_left($card)) continue;

    $left = card_left($card);
    $options[] = [
      'type'  => 'card',
      'card'  => (int)$card['id'],
      'title' => $card['title'],
      'note'  => ($left === null ? 'Без обмежень' : 'лишилось ' . $left)
                 . ', до ' . format_date_short($card['due_date']),
    ];
  }

  $profile = account_profile();
  $balance = (float)($profile['account'] ?? 0);
  $price   = (float)($event['price'] ?? 0);

  // Рахунок показуємо лише якщо на ньому вистачає: варіант, який гарантовано
  // впаде на сабміті, не пропонуємо взагалі.
  if ($balance >= $price && $price > 0) {
    // Ціна заняття, а не весь баланс: сусідні рядки ("лишилось 6",
    // "350 грн") теж показують, скільки саме списується за ЦЕ заняття,
    // а не загальний стан абонемента/рахунка — сирий баланс тут плутав,
    // бо не було зрозуміло, чи 1200 грн стосується цього платежу.
    $options[] = [
      'type'  => 'account',
      'title' => 'Особистий рахунок',
      'note'  => format_price($event['price']) . ', залишиться ' . format_price($balance - $price),
    ];
  }

  $options[] = [
    'type'  => 'online',
    'title' => 'Оплатити карткою',
    'note'  => $price > 0 ? format_price($event['price']) : '',
  ];
  $options[] = [
    'type'  => 'request',
    'title' => 'Забронювати, оплачу в студії',
    'note'  => 'Менеджер підтвердить запис',
  ];

  return $options;
}

/**
 * Українська плюралізація вже написана для сторінки цін — беремо її, а не
 * пишемо другу копію. require_once, бо обидва файли можуть бути підключені
 * на одній сторінці (booking-модалка тягне і розклад, і кабінет).
 */
if (!function_exists('plural_uk')) {
  require_once __DIR__ . '/prices.php';
}

/** «1200.00» → «1 200 грн». Копійки ховаємо, коли вони нульові. */
function format_price($value): string {
  $n = (float)$value;
  // Нерозривний пробіл у розряді: інакше «1 200 грн» ламається по рядках.
  $s = fmod($n, 1.0) == 0.0 ? number_format($n, 0, ',', "\u{00A0}") : number_format($n, 2, ',', "\u{00A0}");
  return $s . "\u{00A0}грн";
}

/**
 * Зона студії. Дати з API несуть власний зсув (+03:00), а сервер за
 * замовчуванням в UTC — без явного переводу заняття о 09:30 показалось би
 * о 06:30. Та сама пастка й те саме рішення, що в api/schedule.php.
 */
function account_tz(): DateTimeZone {
  static $tz = null;
  if ($tz === null) {
    // icu_tz_name() живе в api/schedule.php і знає про старий Europe/Kiev
    // на системах без свіжої ICU. Тут воно є лише тоді, коли розклад уже
    // підключений, тож для самостійного випадку — той самий фолбек.
    if (function_exists('icu_tz_name')) {
      $tz = new DateTimeZone(icu_tz_name());
    } else {
      try {
        $tz = new DateTimeZone('Europe/Kyiv');
      } catch (Throwable) {
        $tz = new DateTimeZone('Europe/Kiev');
      }
    }
  }
  return $tz;
}

/** Рядок дати з API → об'єкт у зоні студії. null, якщо дати немає. */
function account_date(?string $date): ?DateTimeImmutable {
  if (!$date) return null;
  try {
    return (new DateTimeImmutable($date))->setTimezone(account_tz());
  } catch (Throwable) {
    return null;
  }
}

/** «2026-09-30» → «30.09». Для компактних рядків списку. */
function format_date_short(?string $date): string {
  $d = account_date($date);
  return $d ? $d->format('d.m') : '';
}

/**
 * Слот розкладу (api/schedule.php, denormalize) → «17 вересня, 10:00».
 *
 * Окрема функція, а не format_date_long($slot['date'].'T'.$slot['time']):
 * denormalize уже перевів заняття в зону студії й розклав на date+time БЕЗ
 * зсуву, тож склеєний рядок прочитався б як UTC і час поїхав би вдруге
 * (заняття о 10:00 показувалось о 13:00).
 */
function format_slot_datetime(array $slot): string {
  $date = format_date_long($slot['date'] . ' 00:00:00', false);
  return $slot['time'] ? $date . ', ' . $slot['time'] : $date;
}

/** «2026-09-18T09:30:00+03:00» → «18 вересня, 09:30». */
function format_date_long(?string $date, bool $with_time = true): string {
  $d = account_date($date);
  if (!$d) return '';

  $months = [
    1 => 'січня', 'лютого', 'березня', 'квітня', 'травня', 'червня',
    'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня',
  ];
  $out = (int)$d->format('j') . ' ' . $months[(int)$d->format('n')];
  return $with_time ? $out . ', ' . $d->format('H:i') : $out;
}

// HTTP-режим: файл викликали напряму, а не через require зі сторінки.
// Порівнюємо повні шляхи, а не basename — сторінка кабінету теж зветься
// account.php, і по імені файлу прокладка вважала б себе ендпоінтом,
// вивалюючи JSON усередину сторінки (та сама пастка, що в api/prices.php).
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'profile'  => account_profile(),
    'cards'    => account_cards(1),
    'visits'   => account_visits(),
    'deposits' => account_deposits(),
  ], JSON_UNESCAPED_UNICODE);
}
