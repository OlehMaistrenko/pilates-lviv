<?php
$nav = 'team';
$page_title = 'Наша команда — тренери студії «Пілатес Львів»';
$page_description = 'Тренери студії: пілатес Springtone на Cadillac і Reformer, йога, танці, функціональне відновлення. Досвід, напрямки, локації.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим

// Імена, напрямки й цитати — дослівно з діючого pilateslviv.com (ті самі
// сім людей, що в слайдері на головній: слугі мусять збігатись, інакше
// картка з index.php поведе на 404).
// [імʼя, slug, напрямок, ключ напрямку для фільтра й модалки, цитата, досвід]
$team = [
  ['Галина',   'halyna',     'Пілатес Springtone',     'pilates',  'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', '12 років'],
  ['Оксана',   'oksana',     'Пілатес Springtone',     'pilates',  'Я допомагаю людям повернути здоров’я та радість життя', '8 років'],
  ['Сюзанна',  'suzanna',    'Пілатес Springtone',     'pilates',  'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', '10 років'],
  ['Ірина',    'iryna',      'Пілатес Springtone',     'pilates',  'Повернути людині красиву поставу та здорове тіло — це те саме, що повернути віру в себе', '7 років'],
  ['Марʼяна', 'mariana',    'Танці',                  'dance',    'Танець — це спосіб досягнути краси і гармонії, володіючи кожним м’язом', '9 років'],
  ['Вікторія', 'viktoria-f', 'Функціональне відновлення', 'recovery', 'Вже 9 років з його допомогою я тримаю себе у чудовій формі', '9 років'],
  ['Вікторія', 'viktoria-y', 'Йога',                   'yoga',     'Йога — це не спорт, а скоріше мистецтво', '6 років'],
];

// Напрямки для фільтра рахуємо з самого $team, а не окремим списком:
// інакше доданий тренер із новим напрямком мовчки випав би з фільтра.
$directions = [];
foreach ($team as $t) {
  $directions[$t[3]] = ['label' => $t[2], 'count' => ($directions[$t[3]]['count'] ?? 0) + 1];
}

$active = $_GET['direction'] ?? '';
if (!isset($directions[$active])) $active = '';   // невідомий напрямок в URL — показуємо всіх
$visible = $active === ''
  ? $team
  : array_values(array_filter($team, fn($t) => $t[3] === $active));

// Відфільтрована добірка — окрема адреса, тож і title у неї свій
if ($active !== '') {
  $page_title = $directions[$active]['label'] . ' — тренери студії «Пілатес Львів»';
}

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий .hero--inner, що на blog.php
     ============================================================ -->
<section class="hero hero--inner">
  <img class="hero__video" src="assets/img/gallery/4.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Команда</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">КОМАНДА</h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Молодий колектив. Кожен веде свій напрямок і продовжує вчитись —
          на семінарах, тренінгах, майстер-класах.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Сітка тренерів із фільтром за напрямком
     ============================================================ -->
<section class="section">
  <div class="container">
    <!-- Звичайні посилання, не таби: фільтр лишається в URL, тож напрямок
         можна відкрити напряму, зберегти й проіндексувати. JS не потрібен. -->
    <nav class="filters" aria-label="Напрямки">
      <a class="filters__item<?= $active === '' ? ' is-current' : '' ?>"
         href="team.php"<?= $active === '' ? ' aria-current="page"' : '' ?>>Усі</a>
      <?php foreach ($directions as $key => $d): ?>
        <a class="filters__item<?= $active === $key ? ' is-current' : '' ?>"
           href="team.php?direction=<?= $key ?>"<?= $active === $key ? ' aria-current="page"' : '' ?>>
          <?= $d['label'] ?>
          <span class="filters__count"><?= $d['count'] ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <ul class="team-grid">
      <?php foreach ($visible as $i => [$name, $slug, $role, $key, $quote, $exp]): ?>
        <li class="trainer" data-reveal style="--reveal-i: <?= $i % 4 ?>">
          <a class="trainer__link" href="trainer-single.php?trainer=<?= $slug ?>">
            <span class="trainer__media">
              <img src="assets/img/team/<?= $slug ?>.jpg" alt="<?= htmlspecialchars($name) ?>, <?= mb_strtolower($role) ?>"
                   width="900" height="1200" loading="<?= $i < 4 ? 'eager' : 'lazy' ?>">
            </span>
            <span class="trainer__name"><?= $name ?></span>
            <span class="text--sm text--muted"><?= $role ?></span>
            <q class="trainer__quote text--sm"><?= $quote ?></q>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- ============================================================
     03 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<section class="split split--booking split--cta on-dark">
  <div class="split__grid embossed">
    <figure class="split__media">
      <img class="split__layer" src="assets/img/cta.jpg" alt="Групове заняття в залі студії"
           width="2048" height="1365" loading="lazy"
           data-anim="parallax" data-parallax="10">
    </figure>

    <div class="split__body" data-anim="parallax" data-parallax="4">
      <h2 data-reveal="lines">Не знаєте, до кого записатись</h2>
      <p class="text--lead split__lead" data-reveal>
        Подзвоніть — спитаємо, що болить і чого хочете, і підберемо тренера
        під це. Перше заняття все одно знайомство.
      </p>

      <div class="split__actions" data-reveal>
        <button type="button" class="btn btn--filled btn--light" data-modal="booking">Записатись</button>
        <a class="btn btn--outlined" href="schedule.php">Розклад занять</a>
      </div>
    </div>
  </div>
</section>

<?php
$seo_title = 'Тренери студії «Пілатес Львів»';
$seo_text  = <<<HTML
  <p>
    У студії працюють семеро тренерів. Четверо ведуть пілатес за методом
    Springtone — на Cadillac, Reformer і Wall Unit; решта — йогу, танці
    й функціональне відновлення. Кожен веде свій напрямок і продовжує
    вчитись: семінари, тренінги, сертифікаційні курси.
  </p>
  <p>
    Тренера можна обрати самому — за напрямком або за розкладом на зручній
    локації. Якщо не впевнені, з ким починати: подзвоніть, розкажіть, що
    болить і чого хочете досягти, — підберемо. Перше заняття завжди
    індивідуальне, тож тренер побачить, як ви рухаєтесь, до того, як ви
    підете в групу.
  </p>
HTML;

include 'partials/footer.php';
?>
