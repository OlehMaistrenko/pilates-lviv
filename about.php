<?php
$nav = 'about';
$page_title = 'Про студію — «Пілатес Львів»';
$page_description = 'Студія пілатесу на Cadillac і Reformer у Львові з 2015 року. Історія засновниці, команда тренерів, зали й напрямки.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // слайдер тренерів, галерея
$vendor_lightbox = true;    // клік по кадру галереї

// Той самий склад і цитати, що на головній — слугі мусять збігатись із team.php
$team = [
  ['Галина',   'Пілатес на обладнанні',   'Коли мій клієнт каже, що в нього більше не болить спина, — це найбільша втіха для мене', 'halyna'],
  ['Оксана',   'Пілатес на обладнанні',   'Я допомагаю людям повернути здоров’я та радість життя', 'oksana'],
  ['Сюзанна',  'Пілатес на обладнанні',   'Ось вже 10 років я займаюсь тим, що повертаю людям радість руху', 'suzanna'],
  ['Ірина',    'Пілатес на обладнанні',   'Повернути людині красиву поставу та здорове тіло — це те саме, що повернути віру в себе', 'iryna'],
  ['Марʼяна', 'Танці',                   'Танець — це спосіб досягнути краси і гармонії, володіючи кожним м’язом', 'mariana'],
  ['Вікторія', 'Функціональний тренінг',  'Вже 9 років з його допомогою я тримаю себе у чудовій формі', 'viktoria-f'],
  ['Вікторія', 'Йога',                    'Йога — це не спорт, а скоріше мистецтво', 'viktoria-y'],
];

// TODO: реальні сертифікати від клієнта — і назви, і скани. Зараз в усіх
// чотирьох картках один бланк-заглушка (assets/img/cert.jpeg): порожній
// шаблон без імені, курсу й дати, тож він нічого не стверджує. Коли
// прийдуть справжні скани — класти в assets/img/certs/ і міняти лише
// четвертий елемент рядка; назви й роки теж заглушкові.
// [назва, організація, рік, скан відносно assets/img/]
$certs = [
  ['Пілатес на обладнанні: повна програма', 'Міжнародна школа пілатесу', '2015', 'cert.jpeg'],
  ['Пілатес для відновлення після травм',    'Міжнародна школа пілатесу', '2018', 'cert.jpeg'],
  ['Робота з хребтом на Cadillac',           'Семінар для тренерів',      '2021', 'cert.jpeg'],
  ['Пре- і постнатальний пілатес',           'Семінар для тренерів',      '2023', 'cert.jpeg'],
  ['Wunda Chair і Barrel: методика',         'Семінар для тренерів',      '2024', 'cert.jpeg'],
  ['Функціональний тренінг',                 'Міжнародна школа пілатесу', '2025', 'cert.jpeg'],
];

// [назва, що саме нас повʼязує, файл логотипа в assets/img/partners/]
// Реальні логотипи виробників обладнання і сертифікаційних шкіл пілатесу —
// TODO: підтвердити з клієнтом, чиє обладнання і чиї сертифікати саме тут.
$partners = [
  ['Balanced Body',   'Обладнання Cadillac, Reformer і Wall Unit у трьох залах',  'balanced-body'],
  ['Align-Pilates',   'Обладнання для групових і персональних занять',           'align-pilates'],
  ['Merrithew',       'Сертифікаційна програма STOTT PILATES для тренерів',      'merrithew'],
  ['BASI Pilates',    'Міжнародна сертифікація тренерів студії',                 'basi'],
];

// Цінності — як влаштоване заняття. Реальні кадри зі зйомки студії.
// Два різні знімки одного сюжету: [заголовок, текст, фон на весь екран,
// кадр у картці]. Фон може бути вертикальним (object-fit: cover його
// дообріже), а кадр у картці — лише горизонтальний: рамка там 3:2.
$advantages = [
  ['Спершу техніка',       'Менше повторів, але правильних. Пружину додаємо, коли рух уже тримається без підказок.', 'values/technique-1.jpg', 'values/technique-2.jpg'],
  ['Група — до 8 людей',   'Щоб тренер встигав підійти до кожного й поправити руками, а не рахувати повтори вголос.', 'values/group-1.jpg', 'values/group-2.jpg'],
  ['Тренери вчаться далі', 'Щороку — семінари й сертифікаційні курси. Нове на заняття приходить звідти, а не з трендів.', 'values/learning-1.jpg', 'values/learning-2.jpg'],
];

$gallery = [
  ['gallery/1.jpg', 'Вправа на реформері'],
  ['gallery/2.jpg', 'Cadillac'],
  ['gallery/3.jpg', 'Wunda chair'],
  ['gallery/4.jpg', 'Розтяжка на Cadillac'],
  ['gallery/5.jpg', 'Вправа на килимку'],
  ['gallery/6.jpg', 'Розтяжка на реформері'],
];

// Той самий каталог, що в trainings.php. [назва, сторінка, опис, ключ кадру]
$directions = [
  ['Стартова пропозиція', 'training-detail.php?training=intro', 'Знайомство з обладнанням, технікою і термінологією — перед першим повноцінним заняттям.', 'intro'],
  ['Пілатес-Реформер', 'training-detail.php?training=reformer', 'На реформерах і Wall Unit. Точне дозування навантаження, робота з усім тілом.', 'reformer'],
  ['Кадилак', 'training-detail.php?training=cadillac', 'Мат-робота на Cadillac: витягнення хребта у висі на стропах, робота на все тіло.', 'cadillac'],
  ['Wunda Chair і Barrel', 'training-detail.php?training=chair-barrel', 'Складніший рівень на двох видах обладнання одразу: баланс, концентрація, сила.', 'chair-barrel'],
  ['Персональне', 'training-detail.php?training=personal', 'Індивідуальні тренування — програма під ваші цілі й особливості тіла.', 'personal'],
  ['Спліт', 'training-detail.php?training=split', 'Парні й мінігрупові заняття — з тренером на двох чи трьох.', 'split'],
  ['Навчальний центр', 'academy.php', 'Курси для тих, хто хоче викладати пілатес.', 'academy'],
  ['Воркшопи', 'training-detail.php?training=events', 'Щомісячні тематичні зустрічі для тренерів і клієнтів.', 'events'],
];

// Як на головній: реальна тільки перша адреса, TODO Брюховичі й Сихів
$locations = [
  ['Чупринки',  'вул. Б. Романицького, 24а'],
  ['Брюховичі', 'вул. Сагайдачного, 7'],
  ['Сихів',     'просп. Червоної Калини, 62'],
];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер з відео й інтро — на весь екран, як герой головної
     (без .hero--page), тож і заголовок тут плакатного кегля. Абзац
     стоїть у банері поруч із заголовком, а не окремою секцією під ним.
     TODO: окреме відео для about від клієнта
     ============================================================ -->
<section class="hero hero--intro on-dark">
  <video class="hero__video" src="assets/video/hero.mp4" poster="assets/img/hero-poster.jpg"
         autoplay muted loop playsinline aria-hidden="true"></video>
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <!-- порожній перший елемент, як у героя головної: він відтискає
           крихти з лінією на середину екрана (три позиції
           space-between замість двох) -->
      <div></div>

      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Про студію</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">ПРО СТУДІЮ</h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Pilates Lviv — студія пілатесу на професійному обладнанні. Відкрились
          у 2015 році на Романицького й одними з перших у Львові поставили
          Cadillac і Reformer. Сюди приходять зі спиною після офісу, після
          травм і пологів — і ті, хто просто хоче сильне тіло без болю.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Команда — світла секція між темним банером і моховою історією
     ============================================================ -->
<?php
$team_items = $team;
$team_title = 'Команда';
$team_lead  = 'Сім тренерів, у кожного свій напрямок. Під кожним портретом — його власні слова.';
include 'partials/team-slider.php';
?>

<!-- ============================================================
     03 · Історія засновниці — єдине змістове мохове поле сторінки
     ============================================================ -->
<?php include 'partials/about-split.php'; ?>

<!-- ============================================================
     04 · Сертифікати — слайдер сканів, клік відкриває їх на весь екран
     (partials/certs.php; той самий блок на сторінці тренера)
     ============================================================ -->
<?php
$certs_items = $certs;
$certs_lead  = 'Тренери щороку проходять сертифікаційні курси й семінари.';
include 'partials/certs.php';
?>

<!-- ============================================================
     05 · Досягнення — той самий банер із цифрами, що на головній
     ============================================================ -->
<?php
$why_title = 'З 2015 року&nbsp;— <em>три зали</em> і власний навчальний центр';
$why_facts = [
  ['2015', 'рік відкриття'],
  ['3',    'зали у Львові'],
  ['8',    'напрямків тренувань'],
  ['7',    'тренерів у команді'],
];
$why_items = [
  ['Навчальний центр',    'Курси для тих, хто хоче викладати пілатес сам.'],
  ['Воркшопи щомісяця',   'Тематичні зустрічі для тренерів і клієнтів.'],
  ['Cadillac і Reformer', 'Професійне обладнання в студії з першого дня.'],
];
$why_img = 'why.jpg';
include 'partials/why.php';
?>

<!-- ============================================================
     06 · Галерея — світла пауза між двома темними банерами
     ============================================================ -->
<?php
$gallery_items = $gallery;
// $gallery_link  = ['3D-тур студією', 'https://app.lapentor.com/sphere/pilates-2'];
include 'partials/gallery.php';
?>

<!-- ============================================================
     07 · Цінності — фулскрін-пін (partials/advantages.php)
     ============================================================ -->
<?php include 'partials/advantages.php'; ?>

<!-- ============================================================
     08 · Напрямки — той самий .direction, що на trainings.php
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines">Напрямки</h2>
      <a class="btn btn--outlined" href="trainings.php" data-reveal>Усі тренування</a>
    </div>

    <div class="swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":1.2,"spaceBetween":24,"breakpoints":{"769":{"slidesPerView":2.2},"1081":{"slidesPerView":4}}}'>
        <?php /* Не весь каталог: на about це анонс, а не список тренувань —
                 за повним веде кнопка «Усі тренування» в шапці секції.
                 Ріжемо slice-ом, а не окремим масивом: $directions лишається
                 єдиним джерелом правди, спільним із trainings.php. */ ?>
        <ul class="directions__list directions__list--slider swiper-wrapper">
          <?php foreach (array_slice($directions, 0, 5) as [$title, $href, $note, $slug]): ?>
            <li class="direction swiper-slide">
              <a class="direction__link" href="<?= $href ?>">
                <span class="direction__media media-swap">
                  <img class="media-swap__img" src="assets/img/directions/<?= $slug ?>.jpg" alt="" width="2048" height="1365" loading="lazy">
                  <img class="media-swap__img media-swap__hover" src="assets/img/directions/<?= $slug ?>-hover.jpg" alt="" width="2048" height="1365" loading="lazy">
                </span>
                <span class="direction__body">
                  <span class="direction__title"><?= $title ?></span>
                  <span class="card__note text--muted"><?= $note ?></span>
                </span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередній напрямок">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступний напрямок">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     08b · Партнери й клієнти — стрічка логотипів одразу під напрямками,
     без верхнього падінга: напрямки — що тут роблять, партнери — на чому
     й з ким. Слайдер зациклений і сам їде: рядів логотипів менше, ніж
     видно за раз, тож набір дублюється, доки не вистачить на найширшу
     розкладку (як у partials/gallery.php).
     ============================================================ -->
<?php $partner_slides = $partners; while (count($partner_slides) < 12) $partner_slides = array_merge($partner_slides, $partners); ?>
<section class="section pt-0">
  <div class="container">
    <div class="section-head section-head--center">
      <h2 data-reveal="lines">Наші партнери</h2>
    </div>

    <div class="swiper-wrap" data-reveal>
      <!-- --swiper-wrapper-transition-timing-function: linear — стрічка їде
           рівномірно, а не смугами прискорення/гальмування, як дає дефолтний
           ease на кожен слайд. Без freeMode: swiper-bundle.min.css жорстко
           ставить .swiper-free-mode>.swiper-wrapper на ease-out і перебиває
           цю змінну — лінійний автоплей і freeMode тут несумісні.
           delay:1 (не 0 — Swiper трактує 0 як «вимкнено») чергує наступний
           перехід одразу по завершенню поточного, тож рух без видимих пауз. -->
      <div class="swiper" style="--swiper-wrapper-transition-timing-function: linear"
           data-swiper='{"slidesPerView":2,"spaceBetween":40,"loop":true,"grabCursor":true,"speed":3500,"autoplay":{"delay":1,"disableOnInteraction":false},"breakpoints":{"769":{"slidesPerView":3,"spaceBetween":56},"1081":{"slidesPerView":5,"spaceBetween":64}}}'>
        <ul class="partners swiper-wrapper">
          <?php /* дублікати заради loop — читалці вони не потрібні, тож
                   підпис має лише перший прохід, решта декоративні */ ?>
          <?php foreach ($partner_slides as $i => [$name, $note, $logo]): ?>
            <li class="partner swiper-slide" <?= $i < count($partners) ? '' : 'aria-hidden="true"' ?>>
              <img class="partner__logo" src="assets/img/partners/<?= $logo ?>.svg"
                   alt="<?= $i < count($partners) ? htmlspecialchars("$name — $note") : '' ?>" loading="lazy">
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     09 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title     = 'Приходьте <span class="nowrap">подивитись зал</span>';
$cta_text      = 'Перше заняття — знайомство: спробуєте обладнання, поговорите з тренером і вирішите самі.';
$cta_locations = $locations;
include 'partials/cta.php';
?>

<?php
$seo_title = 'Про студію пілатесу «Пілатес Львів»';
$seo_text  = <<<HTML
  <p>
    Pilates Lviv працює у Львові з 2015 року. Засновниця побачила пілатес
    на обладнанні у Нью-Йорку, де він давно став звичною частиною тижня,
    і відкрила студію з Cadillac і Reformer на вулиці Романицького —
    одну з перших у місті.
  </p>
  <p>
    Сьогодні у студії сім тренерів і вісім напрямків: від стартової
    пропозиції й пілатесу на реформерах до персональних тренувань і
    навчального центру для тих, хто хоче викладати сам. Групи — до восьми
    людей, щоб тренер бачив техніку кожного. Перше заняття — знайомство:
    подивитись зал, спробувати обладнання, поговорити з тренером.
  </p>
HTML;

include 'partials/footer.php';
?>
