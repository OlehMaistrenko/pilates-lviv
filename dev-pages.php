<?php
/**
 * Службова сторінка розробника — усі сторінки сайту та їхні стани.
 * НЕ частина сайту: ніде не лінкована, у $sitemap футера не входить.
 *
 * Правило списку: один шаблон — одне посилання. Варіанти, де змінюється
 * лише контент (інше тренування, інша подія, інша локація, стан фільтра),
 * не дублюються — у них та сама верстка. Окремим посиланням іде тільки те,
 * де шаблон реально інший: вкладка кабінету, гостьовий стан, стаття без
 * обкладинки.
 */
$nav = '';
$page_title = 'Сторінки — dev';
$page_description = '';

include 'partials/header.php';

// [заголовок групи => [[підпис, href], …]]
$groups = [
  'Головна й тренування' => [
    ['Головна', 'index.php'],
    ['Список тренувань', 'trainings.php'],
    ['Детальна тренування', 'training-detail.php?training=reformer'],
    ['Навчальний центр', 'academy.php'],
  ],

  'Розклад, ціни, локації, команда' => [
    ['Розклад', 'schedule.php'],
    ['Ціни', 'prices.php'],
    ['Список локацій', 'locations.php'],
    ['Детальна локації', 'location-single.php?loc=chuprynky'],
    ['Уся команда', 'team.php'],
    ['Детальна тренера', 'trainer-single.php?trainer=halyna'],
  ],

  'Про студію, події, співпраця' => [
    ['Про студію', 'about.php'],
    ['Контакти', 'contacts.php'],
    ['Співпраця', 'partnership.php'],
    ['Список подій', 'events.php'],
    ['Детальна події', 'event-single.php?event=spyna-za-stolom'],
  ],

  'Блог — ?cover' => [
    ['Список статей', 'blog.php'],
    ['Стаття з обкладинкою', 'blog-single.php?post=cadillac'],
    ['Стаття без обкладинки', 'blog-single.php?cover=0'],
  ],

  // ?guest вимикає авторизацію в api/account.php — гостьовий стан
  // однаково працює на будь-якій сторінці кабінету.
  'Кабінет — ?tab, ?guest' => [
    ['Персональні дані', 'account.php'],
    ['Гість (не залогінений)', 'account.php?guest'],
    ['Абонементи — активні', 'account-cards.php'],
    ['Абонементи — архів', 'account-cards.php?tab=archive'],
    ['Заняття — майбутні', 'account-visits.php'],
    ['Заняття — минулі', 'account-visits.php?tab=past'],
    ['Платежі', 'account-deposits.php'],
    ['Зміна пароля', 'account-password.php'],
  ],

  'Питання, службові сторінки' => [
    ['Усі питання', 'faq.php'],
    ['Політика конфіденційності', 'privacy.php'],
    ['404', '404.php'],
    ['Модалки — dev', 'dev-modals.php'],
  ],
];
?>

<!-- Інлайн-стиль, а не блок у css/styles.css: сторінка службова, не частина
     дизайн-системи проєкту — не заслуговує на постійне місце в CSS-файлі. -->
<style>
  .dev-pages__grid { display: flex; flex-wrap: wrap; gap: var(--sp-3); }
</style>

<section class="section section--tight-top">
  <div class="container container--narrow">
    <h1 class="section-head__title mb-7">Сторінки</h1>
  

    <?php foreach ($groups as $title => $items): ?>
      <h2 class="account-content__subtitle"><?= htmlspecialchars($title) ?></h2>
      <div class="dev-pages__grid mb-8">
        <?php foreach ($items as [$label, $href]): ?>
          <a class="btn btn--sm btn--outlined" href="<?= htmlspecialchars($href) ?>">
            <?= htmlspecialchars($label) ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
