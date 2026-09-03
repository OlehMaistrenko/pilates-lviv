<?php
$nav = '';
$page_title = 'Студія пілатесу у Львові — Pilates Lviv';
$page_description = 'Пілатес на професійних тренажерах Cadillac і Reformer, йога, танці та функціональне відновлення. Львів, вул. Б. Романицького, 24а. Працюємо з 2015 року.';
$header_over_hero = true;   // герой моховий — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // слайдер відгуків

// Реальний каталог занять із діючого pilateslviv.com
$directions = [
  ['Пілатес Springtone', 'training-pilates.php', 'На Cadillac і Reformer. Пружина замість ваги — м’яко для суглобів, точно для м’язів.', 'pilates'],
  ['Функціональне відновлення', 'training-recovery.php', 'Після пологів, після травми, при болю в спині. Повертаємо рух там, де він зник.', 'recovery'],
  ['Йога', 'training-yoga.php', 'Не спорт, а радше мистецтво: тіло, дихання й увага в одному темпі.', 'yoga'],
  ['Танці', 'training-dance.php', 'Спосіб досягнути краси і гармонії, володіючи кожним м’язом.', 'dance'],
  ['Консультація фізіолога', 'training-physio.php', 'Розбираємо, що саме болить і чому, і складаємо план занять під вас.', 'physio'],
  ['Навчальний центр', 'academy.php', 'Курси для тих, хто хоче викладати пілатес сам.', 'academy'],
];

// Тренери й цитати — дослівно з діючого сайту. Напрямок перших чотирьох
// на сайті не вказаний — виставлено за змістом цитат (спина, постава),
// уточнити в клієнта.
$team = [
  ['Галина',   'Пілатес Springtone',      'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', 'halyna'],
  ['Оксана',   'Пілатес Springtone',      'Я допомагаю людям повернути здоров’я та радість життя', 'oksana'],
  ['Сюзанна',  'Пілатес Springtone',      'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', 'suzanna'],
  ['Ірина',    'Пілатес Springtone',      'Повернути людині красиву поставу та здорове тіло — це те саме, що повернути віру в себе', 'iryna'],
  ['Назарій',  'Танці',                   'Танець — це спосіб досягнути краси і гармонії, володіючи кожним м’язом', 'nazarii'],
  ['Вікторія', 'Функціональний тренінг',  'Вже 9 років з його допомогою я тримаю себе у чудовій формі', 'viktoria-f'],
  ['Вікторія', 'Йога',                    'Йога — це не спорт, а скоріше мистецтво', 'viktoria-y'],
];

// TODO: приклади. Замінити на реальні відгуки клієнтів (Google Maps / Instagram).
$reviews = [
  ['Після другої дитини боліла поперек — на реформері за два місяці пройшло. Тренер щоразу дивиться, як саме я роблю вправу, а не просто рахує повтори.', 'Марта', 'функціональне відновлення'],
  ['Сиджу за ноутбуком по 10 годин. Прийшов, бо не міг повернути голову. Тепер ходжу двічі на тиждень, шия не болить, постава помітно рівніша.', 'Андрій', 'пілатес Springtone'],
  ['Пробувала пілатес у трьох студіях. Тут єдині, де є Cadillac, і єдині, де мені пояснили, навіщо кожна вправа.', 'Оля', 'пілатес Springtone'],
];

$faq = [
  ['Я ніколи не займався пілатесом. З чого почати?',
   'З першого заняття — це знайомство: дивитесь зал, пробуєте тренажери, тренер дивиться, як ви рухаєтесь. Якщо є біль у спині чи травми — краще спершу консультація фізіолога, вона допоможе підібрати напрямок.'],
  ['Що взяти з собою?',
   'Зручний одяг, який не сковує рухи, і шкарпетки — на реформері займаються без взуття. Килимки й усе обладнання є в студії.'],
  ['У мене болить спина. Мені можна?',
   'Так, і саме для цього є напрямок «Функціональне відновлення». Перед першим заняттям розкажіть тренеру про діагноз або принесіть висновок лікаря — програму складуть під вас.'],
  ['Чим Cadillac і Reformer кращі за килимок?',
   'Пружини дають опір, який можна точно дозувати — від дуже легкого до сильного. Це м’якше для суглобів, ніж вільна вага, і дає тренеру контролювати техніку кожного руху.'],
  ['Як записатись і скасувати заняття?',
   'Телефоном або через форму на сайті — передзвонимо й підберемо час. Про скасування попереджайте заздалегідь, щоб місце міг зайняти хтось інший.'],
];

// Галерея: розмір кадру (span у 12-колонковій сітці) і пропорція — у CSS за порядком
$gallery = ['Зал із реформерами', 'Cadillac', 'Роздягальня', 'Вхід зі сторони вулиці', 'Індивідуальне заняття', 'Група на килимках'];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Герой — єдине мохове поле у верхній частині сторінки
     ============================================================ -->
<section class="hero">
  <!-- Відео без звуку в петлі; постер — кадр із нього, показується до
       першого кадру й там, де autoplay заборонений (економія трафіку). -->
  <video class="hero__video" src="assets/video/hero.mp4" poster="assets/img/hero-poster.jpg"
         autoplay muted loop playsinline aria-hidden="true"></video>
  <div class="hero__tint" aria-hidden="true"></div>

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
      </div>

      <p class="hero__meta text--sm" data-reveal style="--reveal-i: 2">
        <span class="hero__meta-item">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
          <span>
            <a href="location-chuprynky.php">Чупринки</a> ·
            <a href="location-bryukhovychi.php">Брюховичі</a> ·
            <a href="location-sykhiv.php">Сихів</a>
          </span>
        </span>
        <a href="<?= $contact['phone_href'] ?>">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
          <?= $contact['phone'] ?>
        </a>
        <span>Пн–Нд, 08:00–21:00</span>
      </p>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Напрямки — нумерований список, а не картки: рядок = напрямок
     ============================================================ -->
<section class="section directions">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Що в нас можна робити</h2>
      <p class="text--lead text--muted" data-reveal>
        Шість напрямків в одній залі. Якщо не знаєте, з чого почати, —
        почніть із консультації: там і розберемось.
      </p>
    </div>

    <ol class="directions__list">
      <?php foreach ($directions as $i => [$title, $href, $note, $slug]): ?>
        <li class="direction" data-reveal style="--reveal-i: <?= $i % 3 ?>">
          <a class="direction__link" href="<?= $href ?>">
            <span class="direction__num label"><?= sprintf('%02d', $i + 1) ?></span>
            <span class="direction__media">
              <span class="ph" role="img" aria-label="<?= htmlspecialchars($title) ?>">
                <span class="ph__name">assets/img/directions/<?= $slug ?>.jpg</span>
              </span>
            </span>
            <span class="direction__body">
              <span class="direction__title"><?= $title ?></span>
              <span class="direction__note text--sm text--muted"><?= $note ?></span>
            </span>
            <svg class="icon direction__arrow" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
          </a>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- ============================================================
     03 · Записатись — ЄДИНЕ умброве поле на сторінці.
     Умбра = колір вуличної вивіски студії, тому означає «двері».
     ============================================================ -->
<section class="booking">
  <div class="container">
    <div class="booking__grid">
      <div class="booking__main">
        <h2 data-reveal="lines">Перше заняття — знайомство</h2>
        <p class="text--lead booking__lead" data-reveal>
          Приходьте подивитись зал, познайомитись із тренером і спробувати
          тренажери. Далі вирішуєте самі.
        </p>

        <ol class="booking__steps">
          <li data-reveal><span class="label">01</span><span>Лишаєте номер або дзвоните — передзвонимо того ж дня.</span></li>
          <li data-reveal style="--reveal-i: 1"><span class="label">02</span><span>Підбираємо напрямок, тренера і час у розкладі.</span></li>
          <li data-reveal style="--reveal-i: 2"><span class="label">03</span><span>Приходите, дивитесь зал і пробуєте Cadillac чи Reformer.</span></li>
        </ol>

        <div class="booking__actions" data-reveal>
          <button type="button" class="btn btn--sand" data-modal="booking">Записатись</button>
          <a class="btn btn--ghost" href="schedule.php">Розклад занять</a>
        </div>
      </div>

      <aside class="booking__aside" data-reveal style="--reveal-i: 1">
        <p class="booking__aside-title">Наші студії</p>
        <ul class="booking__studios">
          <li class="studio">
            <p class="studio__name">Чупринки</p>
            <p class="text--sm text--muted"><?= $contact['address'] ?></p>
            <p class="text--sm text--muted">Пн–Нд, 08:00–21:00</p>
            <a class="link-arrow" href="location-chuprynky.php">
              Розклад
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
            </a>
          </li>
          <!-- TODO: адреса, графік і телефон по цих двох локаціях — від клієнта.
               На діючому сайті вказана лише одна адреса, вигадувати решту не можна. -->
          <li class="studio studio--empty">
            <p class="studio__name">Брюховичі</p>
            <p class="text--sm text--muted">Адреса і графік уточнюються</p>
          </li>
          <li class="studio studio--empty">
            <p class="studio__name">Сихів</p>
            <p class="text--sm text--muted">Адреса і графік уточнюються</p>
          </li>
        </ul>
      </aside>
    </div>
  </div>
</section>

<!-- ============================================================
     04 · Чому саме ми — одна теза, цифри, три аргументи з діючого сайту
     ============================================================ -->
<section class="section why">
  <div class="container">
    <h2 class="why__title" data-reveal="lines">Єдина у Львові студія з професійними тренажерами для пілатесу — <em>Cadillac і Reformer</em></h2>

    <div class="why__grid">
      <dl class="why__facts">
        <div data-reveal><dt>2015</dt><dd class="text--sm text--muted">рік, з якого працюємо у Львові</dd></div>
        <div data-reveal style="--reveal-i: 1"><dt>2</dt><dd class="text--sm text--muted">тренажери — Cadillac і Reformer</dd></div>
        <div data-reveal style="--reveal-i: 2"><dt>6</dt><dd class="text--sm text--muted">напрямків: від пілатесу до танців</dd></div>
        <div data-reveal style="--reveal-i: 3"><dt>7</dt><dd class="text--sm text--muted">тренерів у команді</dd></div>
      </dl>

      <div class="why__list">
        <div class="why__item" data-reveal>
          <h3>Пружина замість ваги</h3>
          <p class="text--muted">
            Cadillac і Reformer дають опір, який можна точно дозувати. Це м’якше
            для суглобів, ніж вільна вага, і дає тренеру бачити техніку кожного руху.
          </p>
        </div>
        <div class="why__item" data-reveal style="--reveal-i: 1">
          <h3>Тренери, які продовжують вчитись</h3>
          <p class="text--muted">
            У нас молодий колектив. Тренери постійно вдосконалюють свої вміння на
            семінарах, тренінгах і майстер-класах — і приносять це в зал, а не
            лишають у сертифікатах.
          </p>
        </div>
        <div class="why__item" data-reveal style="--reveal-i: 2">
          <h3>Метод Springtone</h3>
          <p class="text--muted">
            Найрезультативніша з наших програм: робота на пружинах із постійним
            контролем техніки. Не «відходити тренування», а зробити кожен рух так,
            щоб він рахувався.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     05 · Про студію — історія засновниці, дослівно з діючого сайту
     ============================================================ -->
<section class="section about">
  <div class="container">
    <div class="about__grid">
      <figure class="about__media" data-reveal>
        <div class="ph" role="img" aria-label="Засновниця студії">
          <span class="ph__name">assets/img/founder.jpg</span>
          <span class="ph__note">Портрет у залі, денне світло. Вертикальний кадр 3:4.</span>
        </div>
        <figcaption class="text--sm text--muted">Засновниця студії</figcaption>
      </figure>

      <div class="about__text">
        <h2 data-reveal="lines">Як усе почалось</h2>
        <blockquote class="about__quote" data-reveal>
          За 15 хвилин прогулянки до метро я побачила аж 4 студії пілатесу
        </blockquote>
        <p data-reveal style="--reveal-i: 1">
          Це був Нью-Йорк. «Ньюйорківці дуже практичні, тому вони не будуть
          витрачати час на те, що не дає результату». Пілатес на тренажерах
          там — звична частина тижня, як кава чи метро.
        </p>
        <p data-reveal style="--reveal-i: 2">
          «Я переконана в тому, що бажання дбати про своє тіло є і в нас,
          українців». Так у 2015 році у Львові з’явилась студія з Cadillac
          і Reformer — перша й досі єдина в місті.
        </p>
        <a class="link-arrow" href="about.php" data-reveal style="--reveal-i: 3">
          Про студію
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     06 · Галерея — зал як він є; розміри кадрів різні навмисно
     ============================================================ -->
<section class="section gallery">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Зал на Романицького</h2>
      <a class="link-arrow" href="https://app.lapentor.com/sphere/pilates-2" target="_blank" rel="noopener" data-reveal>
        3D-тур студією
        <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
      </a>
    </div>

    <div class="gallery__grid">
      <?php foreach ($gallery as $i => $alt): ?>
        <figure class="gallery__item" data-reveal style="--reveal-i: <?= $i % 3 ?>">
          <div class="ph" role="img" aria-label="<?= $alt ?>">
            <span class="ph__name">assets/img/gallery/<?= sprintf('%02d', $i + 1) ?>.jpg</span>
            <span class="ph__note"><?= $alt ?></span>
          </div>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     07 · Тренери — портрет-арка, ім’я, напрямок і власна цитата
     ============================================================ -->
<section class="section team">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Тренери</h2>
      <p class="text--lead text--muted" data-reveal>
        Молодий колектив. Кожен веде свій напрямок і продовжує вчитись —
        на семінарах, тренінгах, майстер-класах.
      </p>
    </div>

    <ul class="team__grid">
      <?php foreach ($team as $i => [$name, $role, $quote, $slug]): ?>
        <li class="trainer" data-reveal style="--reveal-i: <?= $i % 4 ?>">
          <a class="trainer__link" href="trainer-<?= $slug ?>.php">
            <span class="trainer__media">
              <span class="ph" role="img" aria-label="<?= $name ?>, <?= mb_strtolower($role) ?>">
                <span class="ph__name">assets/img/team/<?= $slug ?>.jpg</span>
              </span>
            </span>
            <span class="trainer__name"><?= $name ?></span>
            <span class="text--sm text--muted"><?= $role ?></span>
            <q class="trainer__quote text--sm"><?= $quote ?></q>
          </a>
        </li>
      <?php endforeach; ?>
      <li class="trainer trainer--all" data-reveal style="--reveal-i: 3">
        <a class="trainer__link" href="team.php">
          <span class="trainer__media">
            <span class="trainer__all">Уся команда</span>
            <svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
          </span>
        </a>
      </li>
    </ul>
  </div>
</section>

<!-- ============================================================
     08 · Відгуки — третє й останнє мохове поле перед футером
     ============================================================ -->
<section class="reviews">
  <div class="container">
    <div class="reviews__grid">
      <div class="reviews__head">
        <h2 data-reveal="lines">Що кажуть клієнти</h2>
      </div>

      <div class="swiper-wrap reviews__slider" data-reveal>
        <div class="swiper" data-swiper='{"slidesPerView":1,"autoHeight":true,"spaceBetween":48,"loop":true}'>
          <div class="swiper-wrapper">
            <?php foreach ($reviews as [$text, $name, $role]): ?>
              <blockquote class="swiper-slide review">
                <p class="review__text"><?= $text ?></p>
                <footer class="review__meta">
                  <span class="review__name"><?= $name ?></span>
                  <span class="text--sm text--muted"><?= $role ?></span>
                </footer>
              </blockquote>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="reviews__controls">
          <button type="button" class="btn-icon swiper-prev" aria-label="Попередній відгук">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
          </button>
          <div class="swiper-pagination"></div>
          <button type="button" class="btn-icon swiper-next" aria-label="Наступний відгук">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     09 · FAQ
     ============================================================ -->
<section class="section faq">
  <div class="container">
    <div class="faq__grid">
      <div class="faq__head">
        <h2 data-reveal="lines">Питання перед першим заняттям</h2>
        <p class="text--muted" data-reveal>
          Не знайшли своє — подзвоніть:
          <a class="faq__phone" href="<?= $contact['phone_href'] ?>"><?= $contact['phone'] ?></a>
        </p>
      </div>

      <div class="faq__list" data-reveal style="--reveal-i: 1">
        <?php foreach ($faq as $i => [$q, $a]): ?>
          <div class="accordion" data-accordion-group="faq">
            <div class="accordion__summary" role="button" tabindex="0" aria-expanded="false" aria-controls="faq-<?= $i ?>">
              <span class="accordion__title"><?= $q ?></span>
              <span class="accordion__icon" aria-hidden="true"></span>
            </div>
            <div class="accordion__body" id="faq-<?= $i ?>">
              <div class="accordion__body-inner">
                <p class="text--muted faq__answer"><?= $a ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     10 · SEO-текст — вузький стовпчик, читається як довідка, не як банер
     ============================================================ -->
<section class="section seo">
  <div class="container container--narrow">
    <div class="simple-text seo__text">
      <h2>Пілатес у Львові на Cadillac і Reformer</h2>
      <p>
        Pilates Lviv — студія на вулиці Романицького, що працює з 2015 року.
        Ми єдина у Львові пілатес-студія з професійними тренажерами Cadillac
        і Reformer: пружинний опір дозволяє точно дозувати навантаження, тому
        заняття підходять і тим, хто відновлюється після травми чи пологів,
        і тим, хто хоче міцне тіло без болю в спині.
      </p>
      <p>
        Окрім пілатесу за методом Springtone, у студії є йога, танці,
        функціональне відновлення та консультації фізіолога. Для тих, хто
        хоче викладати сам, працює навчальний центр. Перше заняття —
        знайомство: подивитись зал, спробувати тренажери, поговорити з
        тренером. Записатись можна телефоном або через форму на сайті.
      </p>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
