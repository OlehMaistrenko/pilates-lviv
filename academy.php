<?php
$nav = 'academy';
$page_title = 'Навчальний центр — курси інструкторів пілатесу у Львові';
$page_description = 'Курс підготовки інструкторів пілатесу на Mat і Reformer: практика на професійних тренажерах Cadillac, Reformer і Wall Unit, іспит і сертифікат. Львів, студія «Пілатес Львів».';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // переваги, тренери, тарифи курсів, відгуки

// Шаблон детальної сторінки курсу — за зразком training-detail.php.
// Реальні: обладнання студії й викладачки з їхніми цитатами (діючий
// pilateslviv.com). TODO від клієнта: кількість годин, дати потоків,
// ціни курсів, чий сертифікат видається і чи він міжнародний.

$all_events = require __DIR__ . '/partials/events-data.php';

// Хто викладає — форма як у $team на головній: [імʼя, напрямок, цитата, slug]
$team = [
  ['Галина',  'Викладачка курсу, Mat і Reformer', 'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', 'halyna'],
  ['Сюзанна', 'Викладачка курсу, методика', 'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', 'suzanna'],
  ['Ірина',   'Практика, робота з поставою', 'Повернути людині красиву поставу та здорове тіло — це те саме, що повернути віру в себе', 'iryna'],
];

// Потоки — ті самі події, що на events.php, лише ті, що для тренерів.
// Одне джерело (partials/events-data.php), а не копія масиву тут: дата
// потоку мусить збігатись зі сторінкою подій, інакше клієнт правитиме її
// у двох місцях і одне забуде. Годин і склад модуля лежать на детальній
// сторінці події, а не окремою таблицею над списком.
$intakes = array_filter($all_events, fn($e) => $e['type'] === 'teacher');

// Ціни курсів — та сама картка .tariff, що на сторінці цін.
// TODO: реальні суми й умови розстрочки від клієнта.
$tariffs = [
  ['Mat: базовий рівень', '8 тижнів, 64 години', [
    ['Формат', 'Група до 8 осіб'],
    ['Практика', 'У залі студії'],
    ['Іспит', 'Входить у вартість'],
  ], 14000, 'mat-base'],
  ['Reformer: обладнання', '10 тижнів, 80 годин', [
    ['Формат', 'Група до 6 осіб'],
    ['Тренажери', 'Reformer, Cadillac, Wall Unit'],
    ['Іспит', 'Входить у вартість'],
  ], 19000, 'reformer'],
  ['Mat + Reformer', '18 тижнів, 144 години', [
    ['Формат', 'Два курси поспіль'],
    ['Оплата', 'Частинами по модулях'],
    ['Іспит', 'Два, входять у вартість'],
  ], 30000, 'full'],
];

// Переваги — [фото, заголовок, текст]. Фото замість іконки: клієнту
// простіше завантажити кадр, ніж підбирати піктограму.
$benefits = [
  ['gallery/2.jpg',      'Практика на обладнанні', 'Cadillac, Reformer і Wall Unit — не за відео, а руками в залі'],
  ['directions/2.jpeg',  'Малі групи',             'До восьми людей на маті й до шести на тренажерах'],
  ['gallery/6.jpg',      'Викладають практики',    'Ті самі тренерки, що щодня ведуть заняття в студії'],
  ['gallery/1.jpg',      'Стажування в студії',    'Після іспиту можна вести пробні заняття під наглядом'],
];

$reviews = [
  ['Прийшла на курс після трьох років занять для себе. Найцінніше — розбір, чому вправа не йде: тепер я бачу це в клієнта за першу хвилину.', 'Оксана', 'випускниця курсу Mat', '4.jpg'],
  ['Вчилася на реформері вже після Mat. Практики багато — ми не слухали лекції, ми весь час стояли біля тренажера і переставляли пружини.', 'Христина', 'випускниця курсу Reformer', '7.jpg'],
];

$faq = [
  ['Треба мати спортивну освіту, щоб піти на курс?',
   'Ні. Потрібен власний досвід занять — хоча б кілька місяців регулярно, щоб тіло вже знало базові рухи. Медична чи спортивна освіта корисна, але не обовʼязкова.'],
  ['Можна одразу на Reformer, без Mat?',
   'Тільки якщо ви вже викладаєте пілатес і закрили базу деінде. Інакше ні: на тренажері важко пояснити людині рух, який ви самі не вмієте розкласти на маті.'],
  ['Скільки триває заняття і як часто вони?',
   'Двічі на тиждень по три-чотири години. Вечірні потоки — у будні, денні — у вихідні. Пропущене заняття можна відпрацювати з наступним потоком.'],
  ['Що я отримаю в кінці?',
   'Іспит — теорія і проведене заняття — і сертифікат студії. Випускники, які склали іспит, можуть провести пробні заняття в студії під наглядом викладачки.'],
  ['Чи допомагаєте з роботою після курсу?',
   'Вакансії в студії бувають не завжди, але ми першими пишемо випускникам, коли вони зʼявляються. Решті допомагаємо порадою: як рахувати ціну заняття, як говорити з клієнтом про діагноз.'],
];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/directions/academy.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <a href="trainings.php">Тренування</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Навчальний центр</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">НАВЧАЛЬНИЙ <em>ЦЕНТР</em></h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Курси для тих, хто хоче викладати пілатес. Практика — на тих
          самих Cadillac і Reformer, на яких щодня працює студія.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Переваги — свайпер карток
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2 data-reveal="lines">Чому вчитись тут</h2>
    </div>

    <div class="swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":1.2,"spaceBetween":24,"breakpoints":{"769":{"slidesPerView":2.2},"1081":{"slidesPerView":4}}}'>
        <ul class="swiper-wrapper">
          <?php foreach ($benefits as [$img, $title, $text]): ?>
            <li class="benefit-card swiper-slide">
              <span class="benefit-card__media">
                <img src="assets/img/<?= $img ?>" alt="" loading="lazy">
              </span>
              <span class="benefit-card__title"><?= $title ?></span>
              <p class="text--sm text--muted"><?= $text ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередня перевага">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступна перевага">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Загальна інформація — .simple-text: абзац, кадр, таблиця
     в одному потоці, як CMS-стаття (той самий набір тегів, що в
     blog-single.php)
     ============================================================ -->
<section class="section pt-0">
  <div class="container container--narrow">
    <div class="section-head section-head--center">
      <h2 data-reveal="lines">Як влаштований курс</h2>
    </div>

    <div class="simple-text" data-reveal>
      <p class="text--lead">
        Курс складається з модулів. Кожен закінчується іспитом: теорія
        і проведене вами заняття. Модулі можна брати поспіль або з
        паузою — Mat відкриває Reformer, далі спеціальні групи.
      </p>

      <figure>
        <img src="assets/img/gallery/2.jpg" alt="Заняття курсу біля Cadillac у залі студії" loading="lazy">
        <figcaption>Практика йде в тому самому залі, де студія веде заняття</figcaption>
      </figure>

      <p>
        Теорії рівно стільки, скільки треба, щоб пояснити рух: анатомія
        хребта й таза, дихання, протипоказання. Решта часу — практика:
        ви робите вправу самі, потім розкладаєте її для іншого учасника,
        потім виправляєте його техніку вголос.
      </p>

      <p>
        Після іспиту лишається стажування: пробні заняття в студії під
        наглядом викладачки, з розбором після кожного. Беруть усіх, хто
        склав іспит, — дати підбираємо під ваш графік.
      </p>

      <p>
        Хто приходить: тренери інших напрямків, реабілітологи, фізіотерапевти
        і люди, які самі займаються пілатесом кілька років і хочуть вести
        заняття. Спортивна освіта не потрібна — потрібен власний досвід
        занять. Годинник, склад і умови вступу кожного модуля — у деталях
        потоку нижче.
      </p>
    </div>
  </div>
</section>

<!-- ============================================================
     04 · Потоки — спільний блок подій (partials/events.php)
     ============================================================ -->
<?php
$events_items = $intakes;
$events_title = 'Найближчі потоки';
$events_link  = ['Усі події', 'events.php'];
$events_class = 'pt-0';
include 'partials/events.php';
?>

<!-- ============================================================
     05 · Хто викладає
     ============================================================ -->
<?php
$team_items = $team;
$team_title = 'Хто викладає';
$team_all   = false;
$team_class = 'pt-0';
include 'partials/team-slider.php';
?>

<!-- ============================================================
     06 · Відео — голий <iframe> у .simple-text (aspect-ratio стоїть
     на самому iframe, обгортка не потрібна)
     ============================================================ -->
<section class="section pt-0">
  <div class="container container--narrow">
    <div class="section-head section-head--center">
      <h2 data-reveal="lines">Як проходить заняття курсу</h2>
    </div>

    <div class="simple-text" data-reveal>
      <figure>
        <iframe src="https://www.youtube.com/embed/MvjMk6BaMtc" title="Заняття навчального центру студії Пілатес Львів"
                loading="lazy" allowfullscreen></iframe>
        <figcaption>Розбір вправи на реформері: пружини, темп, де тренер тримає руку</figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- ============================================================
     07 · Ціни курсів — та сама картка .tariff, що на prices.php
     ============================================================ -->
<section class="section pt-0">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Вартість</h2>
      <a class="btn btn--outlined" href="prices.php">Ціни на заняття</a>
    </div>

    <div class="tariffs swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":1.15,"spaceBetween":16,"breakpoints":{"769":{"slidesPerView":2.3,"spaceBetween":24},"1081":{"slidesPerView":3,"spaceBetween":24}}}'>
        <ul class="swiper-wrapper">
          <?php foreach ($tariffs as $i => [$title, $subtitle, $rows, $price, $key]): ?>
            <li class="swiper-slide">
              <article class="tariff" data-reveal style="--reveal-i: <?= $i ?>">
                <div class="tariff__head">
                  <h3 class="tariff__title"><?= $title ?></h3>
                  <p class="text--sm text--muted mt-2"><?= $subtitle ?></p>
                </div>

                <ul class="rows tariff__rows">
                  <?php foreach ($rows as [$label, $value]): ?>
                    <li class="rows__item rows__item--tariff">
                      <span class="label text--muted"><?= $label ?></span>
                      <span class="text--sm"><?= $value ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>

                <p class="tariff__price"><?= number_format($price, 0, '.', ' ') ?> ₴</p>

                <button type="button" class="btn btn--filled btn--block tariff__btn"
                        data-modal="callback?course=<?= rawurlencode($title) ?>">Записатись</button>
              </article>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередній курс">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступний курс">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     08 · Запис на курс — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Записатись на курс';
$cta_text  = 'Залиште телефон — передзвонимо, спитаємо про ваш досвід і скажемо, з якого модуля починати.';
$cta_modal = 'callback?course=' . rawurlencode('Навчальний центр');
$cta_btn   = 'Залишити заявку';
$cta_img   = 'gallery/2.jpg';
$cta_img_alt = 'Заняття курсу біля тренажерів у залі студії';
include 'partials/cta.php';
?>

<!-- ============================================================
     09 · Відгуки випускниць — друге мохове поле (третє — футер)
     ============================================================ -->
<?php
$reviews_items = $reviews;
$reviews_title = 'Що кажуть випускниці';
include 'partials/reviews.php';
?>

<!-- ============================================================
     10 · FAQ
     ============================================================ -->
<?php
$faq_items = $faq;
$faq_title = 'Питання про курс';
$faq_group = 'academy-faq';
include 'partials/faq.php';
?>

<?php
$seo_title = 'Курси інструкторів пілатесу у Львові';
$seo_text  = <<<HTML
  <p>
    Навчальний центр студії «Пілатес Львів» готує інструкторів пілатесу
    на маті й на обладнанні. Курс іде модулями: Mat — класичний репертуар
    і анатомія руху, Reformer — пружинний опір і робота на тренажерах,
    окремий модуль — спеціальні групи: вагітність, після пологів, біль у
    спині, відновлення після травми. Кожен модуль закінчується іспитом.
  </p>
  <p>
    Практика йде в залі студії на Cadillac, Reformer і Wall Unit — ми одна
    з перших у Львові студій з професійними тренажерами для пілатесу.
    Групи малі: до восьми людей на маті й до шести на обладнанні, тож
    викладачка встигає подивитись кожного. Викладають діючі тренерки
    студії, а не запрошені лектори. Спортивна освіта для вступу не
    потрібна — потрібен власний досвід занять пілатесом.
  </p>
HTML;

include 'partials/footer.php';
?>
