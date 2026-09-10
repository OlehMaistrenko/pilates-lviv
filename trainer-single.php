<?php
$nav = 'team';
$page_title = 'Галина — пілатес Springtone | Студія «Пілатес Львів»';
$page_description = 'Галина, тренерка пілатесу Springtone. 12 років досвіду, робота зі спиною і поставою на Cadillac і Reformer. Розклад занять і сертифікати.';
$vendor_swiper = true;   // галерея занять
$vendor_lightbox = true; // клік по кадру галереї

// Приклад-демо детальної сторінки тренера: слуг із ?trainer= на статиці
// не читаємо (як і ?post= у blog-single.php) — контент тут один, решту
// віддаватиме CMS. Реальні тільки імʼя, напрямок і цитата (з pilateslviv.com);
// біографія, сертифікати й розклад — TODO: дані від клієнта.

// Інші тренери під низом сторінки — [імʼя, slug, напрямок]
$others = [
  ['Оксана',  'oksana',  'Пілатес Springtone'],
  ['Сюзанна', 'suzanna', 'Пілатес Springtone'],
  ['Ірина',   'iryna',   'Пілатес Springtone'],
];

$gallery = [
  ['gallery/1.jpg',     'Вправа на реформері під наглядом тренера'],
  ['gallery/2.jpg',     'Cadillac у залі студії'],
  ['directions/2.jpeg', 'Робота з поставою на реформері'],
  ['gallery/5.jpg',     'Розтяжка на Cadillac'],
  ['directions/3.jpeg', 'Індивідуальне заняття з тренером'],
];

$certs = [
  ['Springtone Pilates — базовий курс інструктора', 'Springtone Education', '2013'],
  ['Reformer та Cadillac: робота з обладнанням', 'Polestar Pilates', '2015'],
  ['Пілатес при протрузіях і грижах хребта', 'Український центр реабілітації', '2018'],
  ['Пілатес після пологів: відновлення тазового дна', 'BALLance® Method', '2021'],
  ['Робота з осанкою у дорослих', 'Springtone Education', '2024'],
];

$schedule = [
  ['Понеділок', '09:00', 'Пілатес Springtone, група', 'Чупринки'],
  ['Понеділок', '19:00', 'Здорова спина', 'Чупринки'],
  ['Середа',    '09:00', 'Пілатес Springtone, група', 'Чупринки'],
  ['Середа',    '18:00', 'Персональне заняття', 'Брюховичі'],
  ['Пʼятниця', '10:30', 'Пілатес-Реформер, мінігрупа', 'Брюховичі'],
];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Шапка-профіль: портрет-арка, імʼя, цитата, факти, запис
     ============================================================ -->
<section class="section profile-section">
  <div class="container">
    <nav class="breadcrumbs text--sm" aria-label="Хлібні крихти">
      <a href="index.php">Головна</a>
      <span aria-hidden="true">·</span>
      <a href="team.php">Команда</a>
      <span aria-hidden="true">·</span>
      <span aria-current="page">Галина</span>
    </nav>

    <div class="profile">
      <figure class="profile__media">
        <img src="assets/img/team/halyna.jpg" alt="Галина, тренерка пілатесу Springtone"
             width="900" height="1200" loading="eager">
      </figure>

      <div class="profile__body">
        <div class="profile__head">
          <h1 class="post-header__title" data-reveal="lines">Галина</h1>
          <p class="text--lead text--muted" data-reveal>Пілатес Springtone</p>
        </div>

        <q class="profile__quote" data-reveal style="--reveal-i: 1">
          Коли мій клієнт каже, що в нього більше не болить спина, — це
          найбільша втіха для мене
        </q>

        <!-- Конкретика замість гасел: з чим саме до неї приходять і на
             чому вона працює. «Напрямок» тут не дублюємо — він уже
             стоїть підзаголовком під імʼям. -->
        <ul class="profile__tags" data-reveal style="--reveal-i: 2">
          <li>Біль у спині</li>
          <li>Постава</li>
          <li>Після пологів</li>
          <li>Грижі й протрузії</li>
          <li>Персональні заняття</li>
        </ul>

        <dl class="profile__facts" data-reveal style="--reveal-i: 3">
          <div class="profile__fact">
            <dt class="text--sm text--muted">Досвід</dt>
            <dd>12 років</dd>
          </div>
          <div class="profile__fact">
            <dt class="text--sm text--muted">Обладнання</dt>
            <dd>Cadillac, Reformer</dd>
          </div>
          <div class="profile__fact">
            <dt class="text--sm text--muted">Локації</dt>
            <dd>Чупринки, Брюховичі</dd>
          </div>
        </dl>

        <div class="profile__actions" data-reveal style="--reveal-i: 4">
          <button type="button" class="btn btn--filled" data-modal="booking?direction=pilates">
            Записатись до Галини
          </button>
          <a class="btn btn--outlined" href="#schedule">
            Розклад Галини
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Загальна інформація — біографія
     ============================================================ -->
<section class="section pt-0">
  <div class="container container--narrow">
    <div class="simple-text" data-reveal>
      <p class="text--lead">
        Веде пілатес на Cadillac і Reformer. Найчастіше до неї приходять
        зі спиною: сидяча робота, грижі, стан після пологів.
      </p>
      <p>
        У пілатес прийшла з фізичної реабілітації — і досі працює радше як
        реабілітолог, ніж як тренер групи. Перше заняття завжди починає
        однаково: дивиться, як людина стоїть, як сідає і як піднімає руки.
        Аж потім підбирає пружини.
      </p>
      <p>
        Веде і групи, і персональні заняття. З діагнозом або болем радить
        перші три-чотири заняття брати індивідуально: у групі тренер не
        встигне тримати вас під контролем щохвилини, а на старті це саме
        те, що потрібно.
      </p>
      <p>
        Вчиться далі: щороку проходить хоча б один сертифікаційний курс
        або семінар — переважно з роботи з хребтом і з відновлення після
        травм.
      </p>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Сертифікати — список однотипних рядків, тому волосина між
     ними тут виправдана (див. CLAUDE.md: розділювач розвʼязує реальну
     задачу компонування, а не обводить кожен блок).
     ============================================================ -->
<section class="section pt-0">
  <div class="container container--narrow">
    <h2 class="mb-5" data-reveal="lines">Сертифікати</h2>

    <ul class="rows" data-reveal>
      <?php foreach ($certs as [$title, $org, $year]): ?>
        <li class="rows__item rows__item--cert">
          <span class="rows__title"><?= $title ?></span>
          <span class="text--sm text--muted"><?= $org ?></span>
          <span class="label rows__num"><?= $year ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- ============================================================
     04 · Які заняття і де проводить
     ============================================================ -->
<section class="section pt-0" id="schedule">
  <div class="container container--narrow">
    <h2 class="mb-5" data-reveal="lines">Які заняття і де проводить</h2>

    <ul class="rows" data-reveal>
      <?php foreach ($schedule as [$day, $time, $class, $place]): ?>
        <li class="rows__item rows__item--slot">
          <span class="rows__day"><?= $day ?></span>
          <span class="label rows__num"><?= $time ?></span>
          <span class="rows__title"><?= $class ?></span>
          <span class="text--sm text--muted rows__place">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
            <?= $place ?>
          </span>
        </li>
      <?php endforeach; ?>
    </ul>

    <a class="btn btn--outlined mt-5" href="schedule.php">Повний розклад студії</a>
  </div>
</section>

<!-- ============================================================
     05 · Галерея занять — спільний блок (partials/gallery.php)
     ============================================================ -->
<?php
$gallery_items = $gallery;
$gallery_title = 'Заняття';
$gallery_class = 'pt-0';
include 'partials/gallery.php';
?>

<!-- ============================================================
     06 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Записатись на заняття';
$cta_text  = 'Передзвонимо, підберемо час у розкладі Галини і відповімо на питання. Перше заняття — знайомство.';
$cta_modal = 'booking?direction=pilates';
include 'partials/cta.php';
?>

<!-- ============================================================
     07 · Інші тренери — генерична сітка карток
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Інші тренери</h2>
      <a class="btn btn--outlined" href="team.php">Уся команда</a>
    </div>

    <ul class="posts">
      <?php foreach ($others as $i => [$name, $slug, $role]): ?>
        <li class="post" data-reveal style="--reveal-i: <?= $i ?>">
          <a class="post__link" href="trainer-single.php?trainer=<?= $slug ?>">
            <!-- alt порожній навмисно: імʼя вже є текстом самого посилання -->
            <span class="post__media">
              <img src="assets/img/team/<?= $slug ?>.jpg" alt="" width="900" height="1200" loading="lazy">
            </span>
            <span class="post__meta text--xs text--muted">
              <span class="label post__rubric"><?= $role ?></span>
            </span>
            <span class="post__title"><?= $name ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
