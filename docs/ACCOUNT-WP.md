# Кабінет клієнта + запис на тренування — план-ТЗ

Як працює логіка кабінету на InstaSport API, і як писати її **зараз на статиці**
так, щоб переїзд на WordPress був переносом файлів, а не переписуванням.

Джерело правди по API: `https://instasport.ua/doc/api/schema/` (OpenAPI 3.0.3,
81 ендпоінт). Сторінка `/doc/api/` — це Redoc, сама схема лежить за посиланням
вище.

---

## 0. TL;DR — що вирішено

| Питання                       | Рішення                           | Чому                                                 |
| ----------------------------- | --------------------------------- | ---------------------------------------------------- |
| Хто джерело правди по клієнту | **InstaSport**, не WP             | пароль, SMS, блокування, абонементи — усе там        |
| Клієнт = `WP_User`?           | **Ні**                            | інакше дві бази паролів і розсинхрон                 |
| Де живе JWT                   | **на сервері**, у сесії           | XSS не має красти доступ до кабінету                 |
| Що в браузері                 | тільки opaque session id у cookie |                                                      |
| Сесія на WP                   | cookie + транзієнт                | `session_start()` б'ється з page cache               |
| Транспорт на WP               | REST (`/wp-json/pilates/v1/*`)    | не `admin-ajax.php`                                  |
| Готовий плагін                | **немає, пишемо свій** mu-plugin  | InstaSport — ніша, інтеграцій немає                  |
| Membership-плагіни            | **не беремо**                     | вони всі навколо `wp_users` + своїх таблиць підписок |

---

## 1. Модель предметної області

Найважливіше для розуміння всього далі: **окремої сутності «запис» в API немає**.
Запис на тренування = створення **посещения** (`visit`). Навіть запис на анонс,
прив'язаний до тренування, іде через visit — це прямо написано в описі
`POST /profile/announce/{id}/enroll/`.

```
        ┌─────────────┐
        │   Event     │  заняття в розкладі (публічне)
        │  (public)   │  template.card_templates → чим можна платити
        └──────┬──────┘
               │
        POST /profile/visit/  { event, payment_type, card? }
               │
               ▼
        ┌─────────────┐        paid_by_card ─────► ┌─────────────┐
        │   Visit     │                            │  Card       │ абонемент
        │             │        paid (enum) ──┐     │ amount, due │
        └─────────────┘                      │     └─────────────┘
                                             │            ▲
                                             │     POST /profile/card/
                                             ▼            │
                                    спосіб оплати ────────┘
                                    card / account / request / online
```

### 1.1 `payment_type` — уся розв'язка «запис ↔ абонемент ↔ оплата»

Абонемент **не є окремим кроком запису**. Це один із чотирьох способів оплатити
конкретний visit:

| `payment_type` | Сенс                                   | Що потрібно                       |
| -------------- | -------------------------------------- | --------------------------------- |
| `card`         | списати заняття з абонемента           | `card` = id абонемента            |
| `account`      | зняти з особистого рахунку             | —                                 |
| `request`      | бронь без оплати, менеджер підтвердить | —                                 |
| `online`       | разова оплата карткою                  | `next_url`, у відповіді `payment` |

У відповіді `ProfileVisit` це видно як `paid_by_card` (id абонемента) +
`paid` (enum способу: `3` Абон, `5` Бронь, `4` Счет, `2` Liqpay, `16` Монобанк…).

### 1.2 Ключове поле для UI: `EventTemplate.card_templates`

`Event.template.card_templates` — масив id шаблонів абонементів, **якими можна
оплатити саме це заняття**. Без нього ми показували б клієнту його абонемент на
йогу як варіант оплати реформера, і отримували 400 на сабміті.

Правило фільтрації списку оплат:

```
доступні абонементи = GET /profile/card/?status=1
                      ∩ card.template ∈ event.template.card_templates
                      ∩ (card.amount === -1 || лишилось > 0)
```

### 1.3 Життєвий цикл абонемента (`ProfileCard`)

```
POST /profile/card/ {template, payment_type: account|request|online}
   status 1 «Заявка» → 4 «Активний» → 6 «Закінчився по відвідуваннях»
                                    → 7 «Закінчився по строку»
                    → 5 «Заморожений» (paused_due_date)
                    → 8 «Скасований»
```

Поля: `amount` (кількість занять, **`-1` = безліміт**), `due_date`, `transfer`
(дозволена кількість скасувань), `pauses`/`paused_duration`/`paused_due_date`,
`status_detail` і `paid_detail` — **готові локалізовані рядки, не вигадувати свої**.

Обмеження API: у `CreateCardRequest.payment_type` **немає** `card` — абонементом
не можна купити абонемент.

---

## 2. Авторизація

### 2.1 Один ендпоінт на вхід і реєстрацію

Найцінніше в цьому API: `phone_login_signup/` робить і вхід, і реєстрацію.
Розрізняємо **за HTTP-кодом**, а не окремими формами:

```
POST /auth/phone_login_signup/  { phone, password }
  200 { token, refresh }  → існуючий клієнт, впускаємо
  201 (порожньо)          → новий, SMS з кодом пішла
  401                     → заблокований (is_active=False)
  409 integrity_error     → колізія при створенні

POST /auth/phone_verify/  { phone, code }  → 200 { token, refresh }
```

**UI-наслідок:** користувач не обирає «вхід чи реєстрація». Одна форма з двох
полів, система розбирається сама:

```
[ телефон ] [ пароль ] → Далі
     ├── 200 → кабінет
     └── 201 → з'являється поле «код із SMS» → phone_verify → кабінет
```

Телефон — обов'язково з `+` і кодом країни (`^\+\d{10,15}$`), **валідація на
нашому боці**: невалідні номери не реєструються.

### 2.2 Скидання пароля

```
POST /auth/phone_reset_password/        { phone }        → 200, SMS
POST /auth/phone_reset_password_verify/ { phone, code, password } → 200 { token, refresh }
```

Повертає токени — після скидання клієнт одразу залогінений. Це ж використовуємо
для «Зміна паролю» в кабінеті (п. 14 структури сайту).

### 2.3 Як передається токен у запиті

```
Authorization: Bearer <token>
```

**Цього немає в OpenAPI-схемі.** Документ чотири рази пише «токен доступа в
заголовках запроса» і жодного разу не називає заголовок; блоку `securitySchemes`
у схемі немає взагалі, у `/profile/*` немає header-параметрів.

| Заголовок                              | Відповідь                                                 |
| -------------------------------------- | --------------------------------------------------------- |
| `Authorization: Bearer xxx`            | `wrong_token` «Неверный токен доступа» ← схему розпізнано |
| `Authorization: Token\|JWT\|xxx`       | `authorization_required`                                  |
| `X-Token` · `Token` · `X-Access-Token` | `authorization_required`                                  |

`wrong_token` = сервер дійшов до перевірки самого токена. Решта = облікових
даних не побачив. `WWW-Authenticate` у відповіді не віддається.

**`X-API-Key` і `Authorization` — не взаємозамінні й не дублюються:**

| Група                      | Заголовок               |
| -------------------------- | ----------------------- |
| `/public/*`, `/auth/*`     | `X-API-Key`             |
| `/profile/*`, `/manager/*` | `Authorization: Bearer` |

`/profile/*` не скаржиться на відсутність `X-API-Key` — йому потрібен лише токен.
Тому `instasport_call()` додає заголовок за призначенням, а не обидва завжди.

### 2.4 Refresh — прозоро на сервері

`token` короткоживучий, `refresh` теж має строк, але оновлюється безлімітно.
Коли refresh помер — `403 invalid_refresh_token` → повний перелогін.

```php
function api_profile( string $path, string $method = 'GET', ?array $body = null ): array {
    $res = instasport_call( $path, $method, $body, session_token() );

    if ( 401 === $res['code'] ) {                       // expired_token
        $new = instasport_call( '/auth/refresh/', 'POST', [ 'refresh' => session_refresh() ] );
        if ( 200 !== $new['code'] ) {                   // 403 invalid_refresh_token
            session_destroy_client();
            return [ 'code' => 401, 'body' => [ 'code' => 'authentication_required' ] ];
        }
        session_set_tokens( $new['body']['token'], $new['body']['refresh'] );
        $res = instasport_call( $path, $method, $body, session_token() );
    }
    return $res;
}
```

Фронт про токени не знає взагалі — бачить тільки 401 «сесія закінчилась».

### 2.5 Коди помилок API

Задокументовані у схемі: `authentication_failed` · `expired_token` ·
`wrong_parameters` · `permission_denied` · `authentication_required` ·
`user_already_exists` · `invalid_refresh_token` · `integrity_error`

Зустрічаються на практиці, але у схемі відсутні: **`wrong_token`** ·
**`authorization_required`** (у схемі — `authentication_required`).

**Наслідок:** перелік кодів ширший за задокументований, обробник помилок
**не має покладатись на закритий список**. Логіка — за HTTP-статусом
(401 → refresh → перелогін), `code` лише для розрізнення гілок, усе незнайоме
→ загальна помилка з показом `detail`.

`ErrorResponse` = `{ code, detail, action }` — `detail` показуємо користувачу.

---

## 3. Безпека — три правила, які не обговорюються

**1. JWT ніколи не потрапляє в браузер.**
Ні в `localStorage`, ні в JS-змінну, ні в не-HttpOnly cookie. `refresh` дає
безлімітний доступ до чужого кабінету — історії, абонементів, оплат. XSS не
повинен могти його вкрасти. Токен живе тільки в серверному сховищі сесії.

**2. `X-API-Key` теж ніколи не потрапляє в браузер.**
Він прив'язаний до домену та IP (це вже задокументовано в `api/schedule.php:5-7`),
тож із браузера однаково не спрацює — але й світити його не можна.

**3. Cookie — `HttpOnly` + `Secure` + `SameSite=Lax`.**
Оскільки автентифікація на cookie, CSRF реальний → на WP обов'язковий nonce
(`X-WP-Nonce`), на статиці — власний токен у формі.

Додатково:

- Сторінки кабінету — `nocache_headers()` + `DONOTCACHEPAGE`, інакше page cache
  віддасть кабінет одного клієнта іншому. **Це найнебезпечніша помилка в усьому
  проєкті.**
- Rate limit на `/auth/*` (SMS коштують грошей і це вектор абузу).
- Персональні дані клієнта **не дублюємо** в WP — зайвий GDPR-периметр.

---

## 4. Чому клієнт — НЕ `WP_User`

Спокуса: завести `wp_users`, отримати `is_user_logged_in()`, `wp_login_form()`,
nonce, ролі безкоштовно. Ціна виявляється вищою за виграш:

1. **Два джерела правди на пароль.** Пароль валідує InstaSport (він же шле SMS).
   WP захоче свій `user_pass`. Клієнт змінив пароль у застосунку студії — на
   сайті старий. Розсинхрон, який ніхто ніколи не чинить.
2. **`wp_users` стає дзеркалом чужої бази.** Клієнта заблокували в CRM
   (`is_active=False`) — у WP він далі залогінений.
3. **Роль `subscriber` = доступ до `/wp-admin/`.** Треба руками різати wp-admin,
   ховати адмінбар, гейтити REST — робота заради того, чого не просили.
4. **Дублювання персональних даних** без потреби.

**Рішення:** InstaSport — єдине джерело правди, WP — тільки транспорт.
Клієнт кабінету не є WP-юзером взагалі.

З цього ж випливає відмова від MemberPress / Ultimate Member / PMPro: вони
побудовані навколо `wp_users` + власних таблиць підписок, тобто нав'язують
рівно ту подвійну базу, від якої ми відмовляємось.

---

## 5. Сесія

### 5.1 На статиці (зараз)

Нативна PHP-сесія — `session_start()`, токени в `$_SESSION`. Просто і працює.

### 5.2 На WP — cookie + транзієнт

`session_start()` у WP — пастка: ламає page cache, конфліктує з object cache,
багато хостингів сесії ріжуть.

```php
// cookie: pl_sess = 32 випадкові байти (HttpOnly, Secure, SameSite=Lax)
// транзієнт: pl_sess_{id} = [ 'token', 'refresh', 'phone', 'profile_id' ]

$sid = bin2hex( random_bytes( 32 ) );
set_transient( "pl_sess_$sid", $tokens, 30 * DAY_IN_SECONDS );
setcookie( 'pl_sess', $sid, [
    'expires'  => time() + 30 * DAY_IN_SECONDS,
    'path'     => '/',
    'secure'   => is_ssl(),
    'httponly' => true,
    'samesite' => 'Lax',
] );
```

Транзієнти — рідний WP API, працюють і на Redis, і на БД як фолбек.

> **ponytail:** транзієнт на Redis без персистентності може випаруватись →
> клієнта викине на перелогін. Прийнятно для кабінету. Якщо знадобиться
> твердіше — своя таблиця `{$wpdb->prefix}pl_sessions` (~20 рядків).

**Умова переносимості:** уся робота з сесією — за трьома функціями
(`session_token()`, `session_set_tokens()`, `session_destroy_client()`).
Переїзд = переписати тільки їхні тіла.

---

## 6. Архітектура коду

### 6.1 Принцип переносимості

Увесь код ділиться на три шари. **Переїзд на WP чіпає тільки шар 1.**

```
┌─ Шар 1: ТРАНСПОРТ ──────────── переписується на WP ─────┐
│  HTTP-виклик · сесія · роутинг · nonce · конфіг         │
├─ Шар 2: ЛОГІКА ─────────────── переноситься як є ───────┤
│  auth-потік · refresh · фільтр абонементів · мапінг     │
├─ Шар 3: РОЗМІТКА ───────────── переноситься як є ───────┤
│  HTML партіалів · CSS · JS                              │
└─────────────────────────────────────────────────────────┘
```

Правило: **шари 2 і 3 ніколи не викликають ні `curl_*`, ні `wp_remote_*`,
ні `$_SESSION`, ні `$_COOKIE` напряму.** Тільки через функції шару 1.

### 6.2 Файли: статика → WP

| Статика (зараз)               | WP (потім)                      | Шар                                     |
| ----------------------------- | ------------------------------- | --------------------------------------- |
| `api/instasport.php`          | `includes/class-instasport.php` | 1 — переписати на `wp_remote_request()` |
| `api/session.php`             | `includes/class-session.php`    | 1 — `$_SESSION` → cookie+транзієнт      |
| `api/account.php` (роутер)    | `includes/rest.php`             | 1 — `switch` → `register_rest_route`    |
| `api/config.local.php`        | константи у `wp-config.php`     | 1                                       |
| `api/logic/*.php`             | `includes/logic/*.php`          | 2 — **без змін**                        |
| `partials/modals/auth.php`    | той самий файл                  | 3 — **без змін**                        |
| `partials/account/*.php`      | шаблони теми                    | 3 — **без змін**                        |
| `js/main.js`, `js/account.js` | без змін                        | 3 — **без змін**                        |

### 6.3 Контракт шару 1 (пишемо зараз, на WP лише тіла міняються)

```php
// api/instasport.php — ЄДИНЕ місце, що знає про HTTP
// $token === null → шле X-API-Key (для /public/*, /auth/*)
// $token !== null → шле Authorization: Bearer (для /profile/*)
// Обидва заголовки разом не потрібні — див. §2.3
function instasport_call( string $path, string $method = 'GET',
                          ?array $body = null, ?string $token = null ): array;
// → [ 'code' => int, 'body' => array|null ]

// api/session.php — ЄДИНЕ місце, що знає про зберігання сесії
function session_token(): ?string;
function session_refresh(): ?string;
function session_set_tokens( string $token, string $refresh ): void;
function session_destroy_client(): void;
function current_client(): ?array;   // null = не залогінений
```

Ось і все. П'ять функцій сесії + одна HTTP. Решта проєкту користується тільки ними.

### 6.4 Структура плагіна (WP)

Must-use плагін — не вимикається випадково з адмінки:

```
wp-content/mu-plugins/pilates-account/
  pilates-account.php          — bootstrap, хуки
  includes/
    class-instasport.php       — HTTP (wp_remote_request)
    class-session.php          — cookie ↔ транзієнт
    class-auth.php             — login / verify / refresh / logout
    rest.php                   — REST-роути
    guards.php                 — template_redirect, nocache
    logic/                     — ПЕРЕНЕСЕНО ЗІ СТАТИКИ БЕЗ ЗМІН
      booking.php              — фільтр абонементів, створення visit
      cards.php                — мапінг ProfileCard → в'юха
      visits.php
```

---

## 7. Що на WP пишемо самі, а що дає платформа

**Не пишемо (рідне WP):**

| Задача            | WP API                                      |
| ----------------- | ------------------------------------------- |
| роутинг           | `register_rest_route()`                     |
| перевірка доступу | `permission_callback`                       |
| CSRF              | `wp_create_nonce('wp_rest')` + `X-WP-Nonce` |
| HTTP-клієнт       | `wp_remote_request()`                       |
| зберігання сесій  | транзієнти                                  |
| гейт сторінок     | `template_redirect`                         |
| шаблони           | `get_template_part()`                       |
| локалізація       | `__()` / `_e()`                             |

**Пишемо самі — рівно три речі:**

1. обмін cookie ↔ токен InstaSport
2. авторефреш JWT
3. мапінг відповідей API у розмітку

Решта — клей на хуках.

### 7.1 Гарди

```php
// Гейт кабінету
add_action( 'template_redirect', function () {
    if ( ! is_page( 'account' ) ) {
        return;
    }
    if ( ! pl_current_client() ) {
        wp_safe_redirect( home_url( '/?modal=auth' ) );  // той самий ?modal= патерн
        exit;
    }
} );

// Кеш — критично
add_action( 'template_redirect', function () {
    if ( pl_current_client() ) {
        nocache_headers();
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
    }
} );
```

### 7.2 REST-роут

```php
register_rest_route( 'pilates/v1', '/visit', [
    'methods'             => 'POST',
    'callback'            => 'pl_create_visit',
    'permission_callback' => fn() => (bool) pl_current_client(),
    'args'                => [
        'event'        => [ 'required' => true, 'type' => 'integer' ],
        'payment_type' => [ 'required' => true, 'enum' => [ 'card', 'account', 'request', 'online' ] ],
        'card'         => [ 'type' => 'integer' ],
    ],
] );
```

`args` зі схемою — WP валідує сам, руками не перевіряємо.

---

## 8. Карта ендпоінтів

### 8.1 Наші (фронт ходить тільки сюди)

| Статика                        | WP                               | Метод  | Призначення             |
| ------------------------------ | -------------------------------- | ------ | ----------------------- |
| `api/account.php?do=login`     | `/wp-json/pilates/v1/auth/login` | POST   | phone_login_signup      |
| `api/account.php?do=verify`    | `…/auth/verify`                  | POST   | phone_verify            |
| `api/account.php?do=reset`     | `…/auth/reset`                   | POST   | скидання пароля         |
| `api/account.php?do=logout`    | `…/auth/logout`                  | POST   |                         |
| `api/account.php?do=cards`     | `…/cards`                        | GET    | абонементи              |
| `api/account.php?do=visits`    | `…/visits`                       | GET    | історія занять          |
| `api/account.php?do=visit`     | `…/visit`                        | POST   | **запис на тренування** |
| `api/account.php?do=visit&id=` | `…/visit/{id}`                   | DELETE | скасування              |
| `api/account.php?do=deposit`   | `…/deposit`                      | POST   | поповнення рахунку      |

### 8.2 InstaSport (тільки з сервера)

База: `/admin/club/{clubslug}/api/v2`

**Публічні (X-API-Key):**

```
GET  /public/event/            розклад (вже використовується)
GET  /public/event/{id}/       одне заняття
GET  /public/card_template/    вітрина тарифів → сторінка «Ціни»
GET  /public/card_template_group/
POST /public/user_create/      заявка без реєстрації {phone, first_name}
```

**Авторизовані (JWT):**

```
POST   /auth/phone_login_signup/     вхід АБО реєстрація
POST   /auth/phone_verify/           підтвердження SMS
POST   /auth/refresh/
POST   /auth/token/                  {username: phone|email, password}
POST   /auth/phone_reset_password/   + /phone_reset_password_verify/

GET    /profile/info/                профілі (основний + сімейні)
GET    /profile/card/?status=1       абонементи
POST   /profile/card/                купівля абонемента
GET    /profile/visit/               історія записів
POST   /profile/visit/               ЗАПИС НА ТРЕНУВАННЯ
PATCH  /profile/visit/{id}/          доплатити бронь
DELETE /profile/visit/{id}/          скасувати запис
GET    /profile/event/               «тренування, на які клієнт може записатись»
GET    /profile/account_deposit/     + POST — поповнення
```

---

## 9. Сторінки кабінету (п. 14 структури сайту)

| Розділ                 | Джерело                                                  | Примітки                                                                                                                     |
| ---------------------- | -------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------- |
| Персональна інформація | `GET /profile/info/`                                     | `UserPortalProfile`: `name`, `relation_detail`, `account`, `rules_accepted`. Сімейні профілі — окремі записи в тому ж списку |
| Баланс                 | `account` з профілю + `GET /profile/account_deposit/`    | історія поповнень                                                                                                            |
| Історія абонементів    | `GET /profile/card/?status=1` активні, `?status=3` архів | `status_detail`, `paid_detail` — готові рядки                                                                                |
| Історія занять         | `GET /profile/visit/`                                    | минулі й майбутні; майбутні можна скасувати                                                                                  |
| Зміна паролю           | `phone_reset_password/` → `_verify/`                     | через SMS                                                                                                                    |

**Сімейні профілі** — `family_profile_id` у `CreateVisitRequest` і
`CreateCardRequest`. Якщо у клієнта є діти в студії, запис іде на їхній профіль.
У v1 можна не робити, але **поле передбачити в контракті одразу**.

---

## 10. Запис на тренування — цільовий UX

Зараз `partials/modals/booking.php` — форма-заглушка. Стає двостанною.

**Не залогінений** → форма входу, або `public/user_create/` як «передзвоніть мені»
(не вимагає авторизації взагалі — дешевий фолбек).

**Залогінений** → реальні варіанти оплати:

```
Пілатес Reformer · 14 вересня, 09:00 · Чупринки

○ Абонемент «10 занять» — лишилось 6, до 30.09   → payment_type=card, card=45
○ Особистий рахунок — 1200 грн                    → payment_type=account
○ Оплатити карткою — 350 грн                      → payment_type=online
○ Забронювати, оплачу в студії                    → payment_type=request

[ Записатись ]
```

Перший рядок — `GET /profile/card/?status=1`, відфільтрований по
`event.template.card_templates` (див. 1.2).

**Перед показом перевіряємо:** `seats !== 0` і `now < enroll_deadline`
(логіка вже є в `api/schedule.php:164-169`). Скасування — `decline_deadline`

- `card.transfer` (скільки скасувань дозволяє абонемент).

---

## 11. Онлайн-оплата

Єдине нетривіальне місце. `payment_type: online` повертає **не редірект**, а
параметри форми для платіжки:

```json
{ "visit": { … },
  "payment": {
    "action": "https://…",
    "price": "350.00",
    "client_commission": "0.00",
    "parameters": { "data": "…", "signature": "…" }
  } }
```

Структура `parameters` залежить від провайдера:

| Провайдер       | Що робити                                                                  |
| --------------- | -------------------------------------------------------------------------- |
| LiqPay          | POST-форма на `action`: `data`, `signature`                                |
| WayForPay       | POST-форма: `merchantAccount`, `orderReference`, `merchantSignature`…      |
| Fondy           | POST-форма: `order_id`, `merchant_id`, `amount` (у копійках!), `signature` |
| Paysera         | POST-форма: `data`, `sign`                                                 |
| Monobank / MAIB | `parameters === null` → просто `location = action` (GET)                   |

Універсальний обробник на фронті — одна прихована форма, поля з `parameters`
як є, підпис рахує InstaSport:

```js
// ponytail: одна форма на всіх провайдерів — parameters летять як є.
// Monobank/MAIB: parameters === null → прямий перехід.
function goToPayment(payment) {
  if (!payment.parameters) {
    location.href = payment.action;
    return;
  }
  const f = document.createElement("form");
  f.method = "POST";
  f.action = payment.action;
  for (const [k, v] of Object.entries(payment.parameters)) {
    const i = document.createElement("input");
    i.type = "hidden";
    i.name = k;
    i.value = v;
    f.append(i);
  }
  document.body.append(f);
  f.submit();
}
```

Потрібні сторінки повернення: `success_url`, `error_url`, `next_url`.

**Уточнити у клієнта: який провайдер підключений.** Від цього залежить, чи
потрібна форма взагалі (Monobank — ні).

---

## 12. Фронтенд — переїжджає без переписування

Наявна машинерія вже підходить, бо ходить по HTTP за HTML-фрагментами:

- `js/main.js:1037-1123` — AJAX-лоадер модалок. Зміниться **один URL**:
  `partials/modals/loader.php?…` → `/wp-json/pilates/v1/modal?…`
- `js/main.js:1118-1122` — `?modal=auth` з query string. Працює як є,
  на нього ж редіректить гард.
- `data-modal="booking?event=123"` — патерн лишається.
- `js/main.js:1151-1157` — «будь-яка `.form` → модалка подяки» — **тут з'явиться
  реальний submit**, форми кабінету треба виключити з цього перехоплення
  (наприклад, за `[data-remote]`).

**Правило на весь новий JS:** ніяких токенів, ніяких прямих звернень до
`instasport.ua`. Тільки наші ендпоінти + cookie, яку браузер шле сам.

---

## 13. Порядок робіт

| #   | Крок                                                       | Результат                             |
| --- | ---------------------------------------------------------- | ------------------------------------- |
| 1   | `api/instasport.php` — витягти curl+ключ зі `schedule.php` | рефакторинг без зміни поведінки       |
| 2   | `api/session.php` — п'ять функцій сесії                    | контракт зафіксовано                  |
| 3   | `api/account.php` — роутер + login/verify/refresh          | працює авторизація                    |
| 4   | `partials/modals/auth.php` — одна форма, три стани         | можна увійти                          |
| 5   | `booking.php` — реальний `POST /profile/visit/`            | **працює запис**                      |
| 6   | `account.php` + вкладки                                    | кабінет (читання даних — найпростіше) |
| 7   | Онлайн-оплата                                              | коли відомий провайдер                |
| 8   | Переїзд на WP                                              | шар 1 переписати, шари 2–3 скопіювати |

Кроки 1–5 дають робочий запис. Крок 6 — читання, найлегший.

---

## 14. Чеклист перед переїздом на WP

- [ ] Жоден файл поза `api/instasport.php` не викликає `curl_*`
- [ ] Жоден файл поза `api/session.php` не чіпає `$_SESSION` / `$_COOKIE`
- [ ] Жоден JS не знає ні токена, ні `X-API-Key`, ні домену instasport.ua
- [ ] `Authorization: Bearer` ніколи не летить на `/public/*` і навпаки (§2.3)
- [ ] Обробник помилок не падає на незадокументованому `code` (§2.5)
- [ ] Уся бізнес-логіка в `api/logic/` — без суперглобалів і HTTP
- [ ] Ключі й `clubslug` лише в `config.local.php` (у `.gitignore`)
- [ ] Сторінки кабінету віддають `Cache-Control: no-store`
- [ ] Усі рядки UI — українською, в одному місці (під `__()` потім)

---

## 15. Відкриті питання до клієнта

1. **Бойовий `X-API-Key`** — і на який домен/IP прив'язаний.
   `clubslug` вже відомий: **`pilates_lviv`** (визначено підбором — решта
   кандидатів дає «Клуб не найден»). Для розробки можна запросити тестовий
   ключ: він не прив'язаний до домену, але діє обмежений час (див. опис тега
   «Общая информация» у схемі).
2. **Який платіжний провайдер** підключений в InstaSport (визначає розділ 11)
3. Чи ввімкнена **оплата абонементом онлайн**, чи лише бронь із підтвердженням
4. **Тестовий акаунт** клієнта з активним абонементом і історією
5. Чи потрібні **сімейні профілі** (діти) у v1
6. Три локації (Брюховичі / Чупринки / Сихів) — чи всі три заведені в InstaSport
   як окремі `hall`, чи одна (на діючому сайті адреса одна — розбіжність із
   структурою сайту, див. CLAUDE.md)
7. Чи є **ціни/тарифи** в InstaSport (`public/card_template/`), чи заводити руками

---

## Додаток: довідники enum

**`ProfileCard.status`** — 1 Заявка · 2 Заявка на продовження · 3 Не активований ·
4 Активний · 5 Заморожений · 6 Закінчився (по відвідуваннях) · 7 Закінчився
(по строку) · 8 Скасований · 9 Закінчений по заміні · 10 Переоформлений

**`paid`** (спосіб оплати) — −1 Немає · 0 Подарунок · 1 Готівка · 2 Liqpay ·
3 Абонемент · 4 Рахунок · 5 Бронь · 6 Термінал · 11 WayForPay · 13 GooglePay ·
14 ApplePay · 15 Гостьовий візит · 16 Монобанк · 17 Fondy · 18 Онлайн ·
21 IBAN · 22 Сейф

**Фільтр `GET /profile/card/?status=`** (інша шкала!) — 1 Активні · 2 Приховані ·
3 Архів
