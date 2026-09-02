<?php
$nav = '';
$page_title = 'Студія пілатесу у Львові — Pilates Lviv';
$page_description = 'Пілатес на професійних тренажерах Cadillac і Reformer, йога, танці та функціональне відновлення. Львів, вул. Б. Романицького, 24а. Працюємо з 2015 року.';
$header_over_hero = true;   // герой моховий — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // стрічка напрямків

// Реальний каталог занять із діючого pilateslviv.com
$directions = [
  ['Пілатес Springtone', 'training-pilates.php', 'На Cadillac і Reformer. Пружина замість ваги — м’яко для суглобів, точно для м’язів.', 'pilates'],
  ['Функціональне відновлення', 'training-recovery.php', 'Після пологів, після травми, при болю в спині. Повертаємо рух там, де він зник.', 'recovery'],
  ['Йога', 'training-yoga.php', 'Не спорт, а радше мистецтво: тіло, дихання й увага в одному темпі.', 'yoga'],
  ['Танці', 'training-dance.php', 'Спосіб досягнути краси і гармонії, володіючи кожним м’язом.', 'dance'],
  ['Консультація фізіолога', 'training-physio.php', 'Розбираємо, що саме болить і чому, і складаємо план занять під вас.', 'physio'],
  ['Навчальний центр', 'academy.php', 'Курси для тих, хто хоче викладати пілатес сам.', 'academy'],
];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Герой — єдине мохове поле у верхній частині сторінки
     ============================================================ -->
<section class="hero">
  <div class="container">
    <div class="hero__inner">
      <div class="hero__text">
        <h1 class="hero__title" data-reveal="lines">Студія <em>пілатесу</em> у Львові</h1>

        <p class="hero__lead text--lead" data-reveal>
          Cadillac і Reformer, йога, танці та функціональне відновлення.
          Працюємо з 2015 року.
        </p>

        <div class="hero__actions" data-reveal style="--reveal-i: 1">
          <button type="button" class="btn btn--sand" data-modal="booking">Записатись на заняття</button>
          <a class="btn btn--ghost" href="schedule.php">Дивитись розклад</a>
        </div>

        <p class="hero__meta text--sm" data-reveal style="--reveal-i: 2">
          <a href="<?= $contact['map'] ?>" target="_blank" rel="noopener">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
            <?= $contact['address'] ?>
          </a>
          <a href="<?= $contact['phone_href'] ?>">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
            <?= $contact['phone'] ?>
          </a>
        </p>
      </div>

      <!-- Коли з’явиться зйомка, .ph міняється на:
           <video src="assets/video/hero.mp4" poster="assets/img/hero.jpg"
                  autoplay muted loop playsinline></video>
           Розмітка й розміри кадру при цьому не змінюються. -->
      <div class="hero__media" data-reveal>
        <div class="ph ph--on-dark" role="img" aria-label="Тренування на реформері у залі студії">
          <span class="ph__name">assets/img/hero.jpg</span>
          <span class="ph__note">Тіло на реформері, бічне світло, мохова стіна. Вертикальний кадр 3:4.</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Напрямки занять — стрічка арок, беж
     ============================================================ -->
<section class="section directions">
  <div class="container">
    <div class="section-head">
      <h2>Що в нас можна робити</h2>
      <p class="text--lead text--muted directions__intro">
        Шість напрямків в одній залі. Якщо не знаєте, з чого почати, —
        почніть із консультації: там і розберемось.
      </p>
    </div>

    <div class="swiper-wrap directions__wrap">
      <!-- freeMode у Swiper 11 — обʼєкт, не булеве: {"freeMode":true} мовчки
           не вмикається, бо модуль читає params.freeMode.enabled -->
      <div class="swiper" data-swiper='{"freeMode":{"enabled":true},"spaceBetween":16,"grabCursor":true}'>
        <div class="swiper-wrapper">
          <?php foreach ($directions as $i => [$title, $href, $note, $slug]): ?>
            <div class="swiper-slide direction">
              <a class="direction__link" href="<?= $href ?>">
                <span class="direction__media">
                  <span class="ph" role="img" aria-label="<?= htmlspecialchars($title) ?>">
                    <span class="ph__name">assets/img/directions/<?= $slug ?>.jpg</span>
                  </span>
                </span>
                <span class="direction__body">
                  <span class="direction__title"><?= $title ?></span>
                  <span class="direction__note"><?= $note ?></span>
                </span>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Записатись / розклад — ЄДИНЕ умброве поле на сторінці.
     Умбра = колір вуличної вивіски студії, тому означає «двері».
     ============================================================ -->
<section class="booking">
  <div class="container">
    <div class="booking__head">
      <h2 data-reveal>Перше заняття — знайомство</h2>
      <p class="text--lead booking__lead" data-reveal>
        Приходьте подивитись зал, познайомитись із тренером і спробувати
        тренажери. Записатись можна телефоном або через форму.
      </p>
      <div class="booking__actions" data-reveal style="--reveal-i: 1">
        <button type="button" class="btn btn--sand" data-modal="booking">Записатись</button>
        <a class="btn btn--ghost" href="schedule.php">Розклад занять</a>
      </div>
    </div>

    <ul class="booking__studios">
      <li class="studio" data-reveal>
        <p class="studio__name">Чупринки</p>
        <p class="studio__addr"><?= $contact['address'] ?></p>
        <p class="studio__hours">Пн–Нд, 08:00–21:00</p>
        <a class="studio__link" href="location-chuprynky.php">
          Розклад студії
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </a>
      </li>

      <!-- TODO: адреса, графік і телефон по цих двох локаціях — від клієнта.
           На діючому сайті вказана лише одна адреса, вигадувати решту не можна. -->
      <li class="studio studio--empty" data-reveal style="--reveal-i: 1">
        <p class="studio__name">Брюховичі</p>
        <p class="studio__addr">Адреса і графік уточнюються</p>
      </li>
      <li class="studio studio--empty" data-reveal style="--reveal-i: 2">
        <p class="studio__name">Сихів</p>
        <p class="studio__addr">Адреса і графік уточнюються</p>
      </li>
    </ul>
  </div>
</section>

<!-- ============================================================
     04 · Чому саме ми — три аргументи, узяті з діючого сайту
     ============================================================ -->
<section class="section why">
  <div class="container">
    <div class="section-head">
      <h2>Чому саме ми</h2>
    </div>

    <div class="why__grid">
      <div class="why__item" data-reveal>
        <h3 class="why__title">Cadillac і Reformer</h3>
        <p class="why__text">
          Ми єдина у Львові пілатес-студія, де можна позайматись на професійних
          тренажерах для пілатесу — Cadillac і Reformer. Пружина дає опір, який
          не навантажує суглоби так, як вільна вага.
        </p>
      </div>

      <div class="why__item" data-reveal style="--reveal-i: 1">
        <h3 class="why__title">Тренери, які продовжують вчитись</h3>
        <p class="why__text">
          У нас молодий колектив. Тренери постійно розвиваються і вдосконалюють
          свої вміння на семінарах, тренінгах і майстер-класах — і приносять це
          в зал, а не лишають у сертифікатах.
        </p>
      </div>

      <div class="why__item" data-reveal style="--reveal-i: 2">
        <h3 class="why__title">Метод Springtone</h3>
        <p class="why__text">
          Найрезультативніша з наших програм: робота на пружинах із постійним
          контролем техніки. Не «відходити тренування», а зробити кожен рух так,
          щоб він рахувався.
        </p>
      </div>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
