<?php
/**
 * Службова сторінка розробника — кнопки для перегляду всіх модалок і їхніх
 * станів без потреби шукати, з якої сторінки й якими параметрами кожна
 * відкривається. НЕ частина сайту: ніде не лінкована, у $sitemap не входить.
 *
 * Кожна кнопка — звичайний [data-modal="name?query"], той самий механізм,
 * що на реальних сторінках (js/main.js, AJAX-лоадер partials/modals/loader.php).
 * Тут нічого не імітується — це ті самі фрагменти з тими самими параметрами.
 */
header('Cache-Control: no-store');

$nav = '';
$page_title = 'Модалки — dev';
$page_description = '';

require_once __DIR__ . '/api/schedule.php';

// Реальні id заняття з мока: одне майбутнє й доступне (для booking/
// slot-details у "робочому" стані), одне минуле (для стану "запис закритий").
$bookable_id = null;
$past_id = null;
foreach (schedule_query(['start_date' => '2020-01-01', 'end_date' => '2030-01-01'])['results'] ?? [] as $slot) {
  if ($slot['is_bookable'] && !$bookable_id) $bookable_id = $slot['id'];
  if (!$slot['is_bookable'] && !$past_id) $past_id = $slot['id'];
  if ($bookable_id && $past_id) break;
}

/**
 * Групи кнопок. Кожен елемент: [підпис, data-modal-рядок]. Порядок
 * груп — за файлами partials/modals/*, а не за важливістю: це довідник,
 * не сторінка продажу.
 */
$groups = [
  'auth — вхід у кабінет (телефон/SMS)' => [
    ['Вхід (телефон+пароль)', 'auth'],
    ['Код із SMS', 'auth?step=code&phone=%2B380631234567'],
    ['Скидання пароля — телефон', 'auth?step=reset'],
    ['Скидання пароля — новий пароль', 'auth?step=reset-code&phone=%2B380631234567'],
  ],
  'contact-change — зміна контактів у кабінеті' => [
    ['Змінити пошту', 'contact-change?field=email'],
    ['Пошту надіслано', 'contact-change?field=email&step=sent'],
    ['Змінити телефон', 'contact-change?field=phone'],
    ['Код підтвердження телефону', 'contact-change?field=phone&step=code'],
  ],
  'booking — запис на заняття' => array_filter([
    ['Загальна заявка (без напрямку)', 'booking'],
    ['Заявка з напрямком', 'booking?direction=yoga'],
    $bookable_id ? ['Оплата заняття (залогінений)', 'booking?event=' . $bookable_id] : null,
    $past_id ? ['Запис закритий (минуле/без місць)', 'booking?event=' . $past_id] : null,
  ]),
  'slot-details — деталі заняття в розкладі' => array_filter([
    $bookable_id ? ['Заняття знайдено', 'slot-details?event=' . $bookable_id] : null,
    ['Заняття не знайдено', 'slot-details?event=999999'],
  ]),
  'callback — замовити дзвінок' => [
    ['Загальний дзвінок', 'callback'],
    ['Дзвінок по тарифу', 'callback?tariff=' . rawurlencode('Абонемент 10 занять')],
    ['Дзвінок по курсу', 'callback?course=' . rawurlencode('Навчальний центр')],
    ['Дзвінок по події', 'callback?event=' . rawurlencode('Майстер-клас із дихання')],
  ],
  'partnership — запит на співпрацю' => [
    ['Загальний запит', 'partnership'],
    ['Запит із форматом "оренда"', 'partnership?format=rent'],
  ],
  'slot-details / thanks / example' => [
    ['thanks — типовий текст', 'thanks'],
    ['thanks — свій текст', 'thanks?title=' . rawurlencode('Дякуємо!') . '&text=' . rawurlencode('Це кастомний текст подяки.')],
    ['example — заглушка з id', 'example?id=42'],
  ],
];

include 'partials/header.php';
?>

<!-- Інлайн-стиль, а не блок у css/styles.css: сторінка службова, не частина
     дизайн-системи проєкту — не заслуговує на постійне місце в CSS-файлі. -->
<style>
  .dev-modals__grid { display: flex; flex-wrap: wrap; gap: var(--sp-3); }
</style>

<section class="section section--tight-top">
  <div class="container container--narrow">
    <h1 class="section-head__title mb-7">Модалки — прев'ю для розробки</h1>
    <p class="text--muted mb-7">
      Службова сторінка, ніде на сайті не лінкована. Кожна кнопка відкриває
      реальний фрагмент через [data-modal] — той самий шлях, що на живих
      сторінках, з тими самими параметрами.
    </p>

    <?php foreach ($groups as $title => $items): ?>
      <?php if (!$items) continue; ?>
      <h2 class="account-content__subtitle"><?= htmlspecialchars($title) ?></h2>
      <div class="dev-modals__grid mb-8">
        <?php foreach ($items as [$label, $query]): ?>
          <button type="button" class="btn btn--sm btn--outlined" data-modal="<?= htmlspecialchars($query) ?>">
            <?= htmlspecialchars($label) ?>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
