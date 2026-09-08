<?php
$nav = '';
$page_title = 'Студія пілатесу у Львові — Pilates Lviv';
$page_description = 'Пілатес на професійних тренажерах Cadillac і Reformer, йога, танці та функціональне відновлення. Львів, вул. Б. Романицького, 24а. Працюємо з 2015 року.';
$header_over_hero = true;   // герой моховий — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // слайдер відгуків

// Реальний каталог занять із instasport.ua/uk/club/pilates_lviv
$directions = [
  ['Пілатес', 'training-pilates-mat.php', 'На матах: глибокий м’язовий корсет, постава, гнучкість. Для початківців і досвідчених.', 'pilates-mat'],
  ['Пілатес-Реформер', 'training-reformer.php', 'На реформерах і Wall Unit. Точне дозування навантаження, робота з усім тілом.', 'reformer'],
  ['Здорова спина', 'training-back.php', 'З кульками BALLance®. Здоров’я хребта, зняття напруги в спині, постава.', 'back'],
  ['Персональне', 'training-personal.php', 'Індивідуальні тренування — програма під ваші цілі й особливості тіла.', 'personal'],
  ['Спліт', 'training-split.php', 'Парні й мінігрупові заняття — з тренером на двох чи трьох.', 'split'],
];

// Три точки — з цитати Джозефа Пілатеса (10/20/30); тексти під кожну — наші.
// Третій елемент — alt кадру; сам файл по індексу (assets/img/path/PROMPTS.md)
$path = [
  ['10', 'Спина менше втомлюється за столом, поставу тримати легше без нагадувань. Вправи з першого заняття вже робите на важчій пружині.', 'Заняття на реформері: інструктор рукою поправляє положення тазу'],
  ['20', 'Різницю помічають інші: рівніші плечі, інша хода. Ви знаєте вправи по назвах і самі відчуваєте, коли пружина стоїть не та.', 'Робота на Cadillac: витягнення хребта у висі на стропах'],
  ['30', 'Рухи, які на старті збирали по частинах, ідуть одним цілим. Біль, з яким прийшли, — уже не причина приходити.', 'Стійка на реформері в повний зріст, зібраний рух'],
];

// Тренери й цитати — дослівно з діючого сайту. Напрямок перших чотирьох
// на сайті не вказаний — виставлено за змістом цитат (спина, постава),
// уточнити в клієнта.
$team = [
  ['Галина',   'Пілатес Springtone',      'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', 'halyna'],
  ['Оксана',   'Пілатес Springtone',      'Я допомагаю людям повернути здоров’я та радість життя', 'oksana'],
  ['Сюзанна',  'Пілатес Springtone',      'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', 'suzanna'],
  ['Ірина',    'Пілатес Springtone',      'Повернути людині красиву поставу та здорове тіло — це те саме, що повернути віру в себе', 'iryna'],
  ['Марʼяна', 'Танці',                   'Танець — це спосіб досягнути краси і гармонії, володіючи кожним м’язом', 'mariana'],
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

$gallery = ['Вправа на реформері', 'Cadillac', 'Wunda chair', 'Розтяжка на Cadillac', 'Вправа на килимку', 'Розтяжка на реформері'];

// Реальна тільки перша адреса (Романицького, 24а — з діючого сайту) і її
// фото (assets/img/location-1). TODO: Брюховичі й Сихів — адреси й описи
// вигадані, фото тимчасово ті самі — замінити все, коли клієнт дасть дані
// й зйомку по двох нових залах.
$locations = [
  ['Чупринки',  'вул. Б. Романицького, 24а', 'Cadillac, два Reformer і Wall Unit. Група — до 8 людей, є роздягальня з душем.', 'location-1/1.jpg'],
  ['Брюховичі', 'вул. Сагайдачного, 7',      'Два Reformer і зал для матів на 12 килимків. Вікна на сосни, поруч є де лишити авто.', 'location-1/3.jpg'],
  ['Сихів',     'просп. Червоної Калини, 62', 'Reformer, Wall Unit і окрема кімната для персональних занять. Три хвилини від трамвая.', 'location-1/6.jpg'],
];

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

  <div class="container container--full">
    <div class="hero__inner">
      <div></div>
      <!-- Лінія ділить перший екран навпіл: над нею — саме відео, під нею
           заголовок. Підписи — результат, за яким приходять: конкретна
           вигода, яку людина впізнає в собі, без гасел про «мистецтво руху». -->
      <p class="hero__rule label" data-reveal>
        <span>Енергія</span>
        <span>Легкість</span>
        <span>Контроль</span>
      </p>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines"><em>ПІЛАТЕС</em> У ЛЬВОВІ</h1>

        <div class="hero__actions" data-reveal style="--reveal-i: 1">
          <button type="button" class="btn btn--sand" data-modal="booking">Записатись на заняття</button>
          <a class="btn btn--ghost" href="schedule.php">Дивитись розклад</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Шлях новичка — цитата Джозефа Пілатеса як теза, і три кроки
     10 · 20 · 30 занять. Цифра-одометр на пів екрана стоїть тлом по центру
     вʼюпорта (pin + scrub), кроки з кадрами йдуть поверх неї.
     ============================================================ -->
<section class="section path">
  <div class="container">
    <div class="section-head">
      <h2 class="path__title" data-reveal="lines">Через <em>10</em> занять ви відчуєте різницю, через <em>20</em> — побачите її, а через <em>30</em> — отримаєте нове тіло</h2>
      <p class="text--sm text--muted" data-reveal>Джозеф Пілатес</p>
    </div>

    <div class="path__grid">
      <!-- одометр: три цифри в масці, скрол зсуває стовпчик (js/main.js,
           data-anim="odometer"), а pin тримає його по центру екрана. На
           мобілці й при reduced-motion прихований — там цифру показує
           кожен крок сам. -->
      <div class="path__counter" data-anim="pin" data-pin-track=".path__list" aria-hidden="true">
        <div class="path__roll">
          <div data-anim="odometer" data-odometer-for=".path__list">
            <?php foreach ($path as [$n]): ?><span><?= $n ?></span><?php endforeach; ?>
          </div>
        </div>
        <span class="label">занять</span>
      </div>

      <ol class="path__list">
        <?php foreach ($path as $i => [$n, $text, $alt]): ?>
          <li class="path__item" data-anim="focus">
            <span class="path__num"><?= $n ?> <span class="label">занять</span></span>
            <p class="path__text"><?= $text ?></p>
            <figure class="path__shot">
              <img src="assets/img/path/<?= $i + 1 ?>.jpeg" alt="<?= htmlspecialchars($alt) ?>"
                   width="3264" height="1312" loading="lazy">
            </figure>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Напрямки — рядок = напрямок, кадр-арка на всю висоту рядка
     ============================================================ -->
<section class="section directions">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Напрямки</h2>
      <p class="text--lead text--muted" data-reveal>
        П’ять напрямків в одній студії. Якщо не знаєте, з чого почати, —
        почніть із консультації: там і розберемось.
      </p>
    </div>

    <ul class="directions__list">
      <?php foreach ($directions as $i => [$title, $href, $note, $slug]): ?>
        <li class="direction" data-reveal style="--reveal-i: <?= $i % 3 ?>">
          <a class="direction__link" href="<?= $href ?>">
            <span class="direction__media">
              <img src="assets/img/directions/<?= $i + 1 ?>.jpeg" alt="" width="1792" height="2400" loading="lazy">
            </span>
            <span class="direction__body">
              <span class="direction__title"><?= $title ?></span>
              <span class="direction__note text--muted"><?= $note ?></span>
            </span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- ============================================================
     04 · Записатись — split-лейаут (кадр + текст). Фон — градієнт умбри
     в мох: умбра = колір вуличної вивіски, тому секція означає «двері».
     Два паралакс-шари з різними швидкостями (кадр повільніший за текст).
     ============================================================ -->
<section class="split split--booking">
  <div class="split__grid">
    <figure class="split__media">
      <img class="split__layer" src="assets/img/cta.jpg" alt="Групове заняття в залі студії"
           width="2048" height="1365" loading="lazy"
           data-anim="parallax" data-parallax="10">
    </figure>

    <div class="split__body" data-anim="parallax" data-parallax="4">
      <h2 data-reveal="lines">Перше заняття — знайомство</h2>
      <p class="text--lead split__lead" data-reveal>
        Приходьте подивитись зал, познайомитись із тренером і спробувати
        тренажери. Далі вирішуєте самі.
      </p>

      <div class="split__actions" data-reveal>
        <button type="button" class="btn btn--sand" data-modal="booking">Записатись</button>
        <a class="btn btn--ghost" href="schedule.php">Розклад занять</a>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     05 · Локації — фулскрін-пін: фон-зал міняється жалюзі, арка тримає
     інфо про поточний зал. Розмітка розрахована на JS (js/main.js,
     data-anim="locations"); без нього видно перший зал статично.
     ============================================================ -->
<section class="locations" data-anim="locations">
  <div class="locations__stage">
    <!-- По шару на зал, цілим кадром. Жалюзі — маска зі смуг
         (mask-image, збирає js/main.js): смуги розширюються, і кадр
         проступає планками, а не одним фейдом. -->
    <div class="locations__bgs" aria-hidden="true">
      <?php foreach ($locations as $l): ?>
        <div class="locations__bg" style="background-image: url('assets/img/<?= $l[3] ?>')"></div>
      <?php endforeach; ?>
    </div>

    <div class="locations__arch">
      <!-- Маркер живе в арці, а не в панелі: він однаковий для всіх залів,
           тож не бере участі в зміні слайдів і нічим не анімується -->
      <svg class="locations__pin icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>

      <!-- Панелі складені в один стек (grid-area 1/1); активну веде скрол.
           Заголовок секції — h2 лише в першій панелі, решта — p: три h2 з
           різними текстами читались би як три різні секції. -->
      <?php foreach ($locations as $i => $l): ?>
        <article class="locations__panel<?= $i ? '' : ' is-active' ?>">
          <?php if ($i === 0): ?>
            <h2 class="locations__title"><?= $l[0] ?></h2>
          <?php else: ?>
            <p class="locations__title"><?= $l[0] ?></p>
          <?php endif; ?>
          <p class="locations__addr">Львів, <?= $l[1] ?></p>
          <p class="locations__desc"><?= $l[2] ?></p>
          <a class="btn btn--block" href="locations.php">
            Про локацію
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<!-- ============================================================
     07 · Про студію — історія засновниці, дослівно з діючого сайту
     ============================================================ -->
<section class="section about">
  <div class="container">
    <div class="about__grid">
      <figure class="about__media" data-reveal>
        <img src="assets/img/founder.jpeg" alt="Засновниця студії" width="1264" height="843" loading="lazy">
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
     06 · Чому саме ми — банер: фонове фото з паралаксом, ряд цифр,
     пунктирний розділювач, три аргументи знизу.
     ============================================================ -->
<section class="why">
  <div class="why__bg" aria-hidden="true">
    <img class="why__layer" src="assets/img/gallery/1.jpg" alt=""
         width="2048" height="1365" loading="lazy"
         data-anim="parallax" data-parallax="12">
  </div>

  <div class="container why__inner">
    <h2 class="why__title" data-reveal="lines">Єдина у Львові студія з професійними тренажерами для пілатесу — <em>Cadillac і Reformer</em></h2>

    <dl class="why__facts">
      <div data-reveal><dt>2015</dt><dd class="text--sm text--muted">рік у Львові</dd></div>
      <div data-reveal style="--reveal-i: 1"><dt>2</dt><dd class="text--sm text--muted">тренажери — Cadillac і Reformer</dd></div>
      <div data-reveal style="--reveal-i: 2"><dt>6</dt><dd class="text--sm text--muted">напрямків тренувань</dd></div>
      <div data-reveal style="--reveal-i: 3"><dt>7</dt><dd class="text--sm text--muted">тренерів у команді</dd></div>
    </dl>

    <div class="why__list">
      <div class="why__item" data-reveal>
        <h3>Пружина замість ваги</h3>
        <p class="text--muted text--sm">М’якше для суглобів, техніка кожного руху видно тренеру.</p>
      </div>
      <div class="why__item" data-reveal style="--reveal-i: 1">
        <h3>Тренери, які вчаться</h3>
        <p class="text--muted text--sm">Молодий колектив, постійні семінари й майстер-класи.</p>
      </div>
      <div class="why__item" data-reveal style="--reveal-i: 2">
        <h3>Метод Springtone</h3>
        <p class="text--muted text--sm">Робота на пружинах із постійним контролем техніки.</p>
      </div>
    </div>
  </div>
</section>


<!-- ============================================================
     08 · Галерея — зал як він є; розміри кадрів різні навмисно
     ============================================================ -->
<section class="section gallery">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Зали та тренажери</h2>
      <a class="link-arrow" href="https://app.lapentor.com/sphere/pilates-2" target="_blank" rel="noopener" data-reveal>
        3D-тур студією
        <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
      </a>
    </div>
  </div>

  <div class="swiper-wrap gallery__slider" data-reveal>
    <div class="swiper" data-swiper='{"effect":"coverflow","grabCursor":true,"centeredSlides":true,"slidesPerView":1.3,"loop":true,"autoplay":{"delay":2800,"disableOnInteraction":false},"coverflowEffect":{"rotate":35,"stretch":0,"depth":220,"modifier":1,"slideShadows":false},"breakpoints":{"768":{"slidesPerView":2},"1080":{"slidesPerView":3}}}'>
      <div class="swiper-wrapper">
        <?php foreach ($gallery as $i => $alt): ?>
          <figure class="swiper-slide gallery__item">
            <img src="assets/img/gallery/<?= $i + 1 ?>.jpg" alt="<?= htmlspecialchars($alt) ?>" loading="lazy">
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     09 · Тренери — портрет-арка, ім’я, напрямок і власна цитата
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

    <div class="swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":2,"spaceBetween":24,"breakpoints":{"769":{"slidesPerView":3},"1081":{"slidesPerView":4}}}'>
        <ul class="swiper-wrapper">
          <?php foreach ($team as $i => [$name, $role, $quote, $slug]): ?>
            <li class="trainer swiper-slide">
              <a class="trainer__link" href="trainer-<?= $slug ?>.php">
                <span class="trainer__media">
                  <img src="assets/img/team/<?= $slug ?>.jpg" alt="<?= htmlspecialchars($name) ?>, <?= mb_strtolower($role) ?>" loading="lazy">
                </span>
                <span class="trainer__name"><?= $name ?></span>
                <span class="text--sm text--muted"><?= $role ?></span>
                <q class="trainer__quote text--sm"><?= $quote ?></q>
              </a>
            </li>
          <?php endforeach; ?>
          <li class="trainer trainer--all swiper-slide">
            <a class="trainer__link" href="team.php">
              <span class="trainer__media">
                <svg class="trainer__all-mark" viewBox="235 127 135 139" aria-hidden="true"><path d="M341.2,147.2c-5.3-1.7-15.6-2.5-22.3,0v-11.9s6.1,4.8,6.1,4.8l4.9-4.8,4.9,4.8,6.4-4.8v11.9Z"/><path d="M261.6,220.1l-15.8-29.5s4.3-1,4.1-1.6c-.7-2.4-5.4-.8-5.4-.8l-1.7-3.2c1-.2,8.7-2.8,12.7,2.2,2.8,3.5,9.9,17.7,10.3,18.1,4.6,1.4,21.7,5.7,25.8,7.4s11.8,15.4,11.8,15.4c2.5-4.1,11.4-19.7,4.7-23.1-3.5-1.7-29.9-8.1-34.7-9.4l-3.7-7.5s4-1.1,3.8-1.6c-.7-2.4-5.1-.9-5.1-.9l-1.3-2.4c2.6-.8,9.1-1.4,11.4,1.5,1.1,1.4,2.1,3,2.1,3,0,0,29.9.3,33.5.2,3.6,0,6.8,0,9.9-1.4s8.4-10.2,4.5-18.9c5.6-3,0-10.2-5.4-4.1,0,0,3.2,4.8,3.7,9.7s.2,7-3.7,9.8c0,0-1.1-2.8-2-2.9-2.9-.3-10.5,10.5-11.5.3,0,0,4.3.9,5.2-.3,2.7-3.8-1.8-4.2-6.9-1.8l-4.3-6.1,7.6-6.6-.4-1.1c-.3-.7,0-1.5.6-1.8l5.9-4.2-3-5.6c10.9-5.8,30.3-4.3,39.3,3.9,15.1,13.6,8,35.8-4.4,41.4,0,0-21.6,40.7-27.8,52.4s4,1.7,4.7,1.7c8.1,0,9.7-9.2,11.5-20.2,2.2-13.8,8.1-18.7,13.4-19,3-.1,7.5,0,9.1,0s.5.5.4.8c-.7,1.3-3.8,7.2-5.2,9.9-1.1,2.1-2.7,2.3-5,2.3-5.7,0-7.3,2.1-8.7,10.4-1.6,11.6-4.8,21.6-18.7,21.6h-30.9s-13.7-25.5-17-31.4c-2.9-4.3-8.2-6.6-13.3-6.5ZM319.1,165.7s-3.2-.5-5,1.3-2.1,3.3-2,3.4c0,0,4-.4,4.4-.7,1.9-2,2.7-4,2.7-4Z"/></svg>
                <span class="trainer__all">Уся команда</span>
              </span>
            </a>
          </li>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn-icon btn-icon--ghost swiper-prev" aria-label="Попередній тренер">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn-icon btn-icon--ghost swiper-next" aria-label="Наступний тренер">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     10 · Відгуки — третє й останнє мохове поле перед футером
     ============================================================ -->
<section class="reviews">
  <div class="container">
    <div class="reviews__grid">
      <div class="reviews__head">
        <h2 data-reveal="lines">Що кажуть клієнти</h2>
      </div>

      <div class="swiper-wrap" data-reveal>
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
        <div class="slider-controls">
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
     11 · FAQ
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
     12 · SEO-текст — вузький стовпчик, читається як довідка, не як банер
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
