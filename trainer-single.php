<?php
$nav = 'team';
$page_title = 'Галина — пілатес на тренажерах | Студія «Пілатес Львів»';
$page_description = 'Галина, тренерка пілатесу на тренажерах. 12 років досвіду, робота зі спиною і поставою на Cadillac і Reformer. Розклад занять і сертифікати.';
$vendor_swiper = true;   // галерея занять
$vendor_lightbox = true; // клік по кадру галереї
$header_over_hero = true; // моховий банер темний — хедер лягає поверх нього прозорим

// Приклад-демо детальної сторінки тренера: слуг із ?trainer= на статиці
// не читаємо (як і ?post= у blog-single.php) — контент тут один, решту
// віддаватиме CMS. Реальні тільки імʼя, напрямок і цитата (з pilateslviv.com);
// біографія, сертифікати й розклад — TODO: дані від клієнта.

$gallery = [
  ['gallery/1.jpg',     'Вправа на реформері під наглядом тренера'],
  ['gallery/2.jpg',     'Cadillac у залі студії'],
  ['directions/2.jpeg', 'Робота з поставою на реформері'],
  ['gallery/5.jpg',     'Розтяжка на Cadillac'],
  ['directions/3.jpeg', 'Індивідуальне заняття з тренером'],
];

// TODO: реальні скани від клієнта — поки в усіх картках один бланк-заглушка
// (assets/img/cert.jpeg), як на about.php. [назва, організація, рік, скан]
$certs = [
  ['Пілатес на тренажерах — базовий курс інструктора', 'Школа інструкторів пілатесу', '2013', 'cert.jpeg'],
  ['Reformer та Cadillac: робота з обладнанням', 'Polestar Pilates', '2015', 'cert.jpeg'],
  ['Пілатес при протрузіях і грижах хребта', 'Український центр реабілітації', '2018', 'cert.jpeg'],
  ['Пілатес після пологів: відновлення тазового дна', 'BALLance® Method', '2021', 'cert.jpeg'],
  ['Робота з осанкою у дорослих', 'Школа інструкторів пілатесу', '2024', 'cert.jpeg'],
];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Шапка-профіль: скруглений портрет, імʼя, цитата, факти, запис
     ============================================================ -->
<section class="section profile-section patterned on-dark">
  <div class="container">
    <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
      <a href="index.php">Головна</a>
      <span aria-hidden="true">·</span>
      <a href="team.php">Команда</a>
      <span aria-hidden="true">·</span>
      <span aria-current="page">Галина</span>
    </nav>

    <div class="profile">
      <figure class="profile__media">
        <img src="assets/img/team/halyna.jpg" alt="Галина, тренерка пілатесу на тренажерах"
             width="900" height="1200" loading="eager">
      </figure>

      <div class="profile__body">
        <div class="profile__head">
          <h1 class="post-header__title" data-reveal="lines">Галина</h1>
          <p class="text--lead text--muted" data-reveal>Пілатес на тренажерах</p>
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

    <!-- Біографія — у тій самій моховій шапці, під сіткою профілю:
         окремою бежевою секцією вона відривалась від імені й фактів,
         хоча розповідає про них же. -->
    <div class="profile__bio simple-text" data-reveal>
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
     02 · Які заняття і де проводить — спільний календар
     (partials/schedule.php), пришпилений до цього тренера.
     Без pt-0: секція йде одразу за моховою шапкою, тобто починає
     нове бежеве поле.
     ============================================================ -->
<?php
$schedule_trainer = 3;   // TODO: id інструктора з CMS замість константи
$schedule_title = 'Які заняття і де проводить';
$schedule_link = ['Повний розклад студії', 'schedule.php'];
include 'partials/schedule.php';
?>

<!-- ============================================================
     03 · Сертифікати — той самий слайдер сканів, що на about.php
     (partials/certs.php)
     ============================================================ -->
<?php
$certs_items = $certs;
$certs_lead  = 'Щороку — хоча б один сертифікаційний курс або семінар.';
$certs_group = 'trainer-certs';
include 'partials/certs.php';
?>

<!-- ============================================================
     04 · Галерея занять — спільний блок (partials/gallery.php)
     ============================================================ -->
<?php
$gallery_items = $gallery;
$gallery_title = 'Заняття';
$gallery_class = 'pt-0';
include 'partials/gallery.php';
?>

<!-- ============================================================
     05 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Записатись на заняття';
$cta_text  = 'Передзвонимо, підберемо час у розкладі Галини і відповімо на питання. Перше заняття — знайомство.';
$cta_modal = 'booking?direction=pilates';
include 'partials/cta.php';
?>

<?php include 'partials/footer.php'; ?>
