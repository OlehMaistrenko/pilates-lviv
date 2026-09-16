<?php
$nav = 'partnership';
$page_title = 'Співпраця — студія «Пілатес Львів»';
$page_description = 'Корпоративні заняття у студії та в офісі, знижки для співробітників, оренда залу з Cadillac і Reformer тренерам, співпраця з лікарями, зйомки й спільні події.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // свайпери переваг і пропозицій + відгуки
$vendor_lightbox = true;    // відео-відгук відкривається в GLightbox

// Аргументи для партнера — та сама форма, що $benefits на сторінці
// тренування: [фото, заголовок, текст]. Фактура реальна (обладнання,
// три локації, рік заснування).
$benefits = [
  ['gallery/2.jpg',     'Повний набір тренажерів', 'Cadillac, Reformer і Wall Unit в одному залі — єдина така студія у Львові'],
  ['gallery/3.jpg',     'Три локації',             'Чупринки, Брюховичі й Сихів — обираєте зал, зручний команді чи клієнтам'],
  ['directions/2.jpeg', 'Сертифіковані тренери',   'Щороку семінари й курси. Заняття веде тренер, а не інструктор за відео'],
  ['gallery/1.jpg',     'Студія з 2015 року',      'Десять років роботи й власна клієнтська база у Львові'],
];

// Пропозиції — картки .tariff зі сторінки цін, але масив статичний: це не
// абонементи з InstaSport, а формати співпраці.
// [ключ формату, назва, опис, [[умова, значення], …], ціна]
// Ключ їде в модалку: data-modal="partnership?format=…" одразу ставить
// потрібний пункт у селекті.
// TODO: усі ціни й відсотки знижок — від клієнта, поки null/заглушки.
$offers = [
  ['private', 'Приватне заняття для команди',
   'Група на реформерах або Cadillac у зручний для вас час. Можна взяти одне заняття, а можна викупити зал цілком — тоді Reformer і Cadillac працюють паралельно.',
   [['У групі', 'до 8 людей'], ['Викуп залу', 'дві групи одночасно']], null],

  ['office', 'Пілатес у вашому офісі',
   'Якщо незручно їхати до студії — тренер приїде до вас. Мат-пілатес зі своїм інвентарем, під будь-який рівень підготовки.',
   [['У групі', 'до 15 людей'], ['Де', 'ваш офіс у Львові']], null],

  ['corporate', 'Знижки для співробітників',
   'Реєструємо компанію як партнера: ознайомче заняття для команди зі знижкою і постійна знижка на абонементи для всіх співробітників.',
   [['Ознайомче', 'зі знижкою'], ['Далі', 'знижка на абонементи'], ['Пакет занять', 'на всю команду']], null],

  ['rent', 'Оренда залу тренерам',
   'Погодинна оренда залу з Cadillac, Reformer і Wall Unit — для тренерів, які ведуть власних клієнтів.',
   [['Формат', 'погодинно'], ['Локації', 'три зали у Львові']], null],

  ['medical', 'Лікарі та фізіотерапевти',
   'Беремо пацієнтів після травм і операцій за протоколом від лікаря. Тренер пише, що вже можна, а що ще ні.',
   [['Формат', 'персональні заняття'], ['Звʼязок', 'після кожного блоку занять']], null],

  ['brand', 'Бренди, зйомки, події',
   'Зал під фотозйомку, спільні воркшопи й колаборації. Найзручніший час — вікна між заняттями.',
   [['Час', 'у вікнах між заняттями'], ['Простір', 'зал із тренажерами']], null],
];

// TODO: реальні відгуки партнерів від клієнта — зараз три заглушки.
$reviews = [
  ['Возимо команду на Чупринки щосереди. Домовились на постійний час, тренер знає, у кого яка спина, — люди ходять самі, нагадувати не треба.', 'Ірина', 'HR, продуктова компанія', '5.jpg'],
  ['Направляю сюди пацієнтів після операцій на коліні. Тренер читає висновок і не тягне навантаження наперед — з таким партнером спокійно.', 'Андрій', 'реабілітолог', '7.jpg'],
  ['Орендую зал двічі на тиждень для своїх клієнтів. Обладнання в порядку, за часом нікого не виганяють — зайшов і працюєш.', 'Христина', 'персональний тренер', '4.jpg'],
];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий .hero--page, що на trainings/locations
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/gallery/5.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Співпраця</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">СПІВПРАЦЯ</h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Працюємо з компаніями, тренерами, лікарями й брендами: заняття для
          команди, оренда залу, спільні події. Якщо вашого формату немає в
          списку — напишіть, обговоримо.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Переваги — той самий свайпер .benefit-card, що на сторінці
     тренування
     ============================================================ -->
<section class="section section--tight-top">
  <div class="container">
    <div class="section-head">
      <h2 data-reveal="lines">Чому з нами</h2>
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
     03 · Що можемо запропонувати — картки .tariff зі сторінки цін.
     Свайпер тут не лише заради гортання: ряди карток (назва/умови/
     ціна/кнопка) задає саме .tariffs .swiper-wrapper через subgrid.
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2 data-reveal="lines">Що можемо запропонувати</h2>
    </div>

    <div class="tariffs swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":1.15,"spaceBetween":16,"breakpoints":{"769":{"slidesPerView":2.3,"spaceBetween":24},"1081":{"slidesPerView":3,"spaceBetween":24}}}'>
        <ul class="swiper-wrapper">
          <?php foreach ($offers as $i => [$key, $title, $note, $rows, $price]): ?>
            <li class="swiper-slide">
              <article class="tariff" data-reveal style="--reveal-i: <?= $i % 3 ?>">
                <div class="tariff__head">
                  <h3 class="tariff__title"><?= $title ?></h3>
                  <p class="text--sm text--muted mt-3"><?= $note ?></p>
                </div>

                <ul class="rows tariff__rows">
                  <?php foreach ($rows as [$label, $value]): ?>
                    <li class="rows__item rows__item--tariff">
                      <span class="label text--muted"><?= $label ?></span>
                      <span class="text--sm"><?= $value ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>

                <?php if ($price !== null): ?>
                  <p class="tariff__price"><?= $price ?> ₴</p>
                <?php endif; ?>

                <button type="button" class="btn btn--filled btn--block tariff__btn"
                        data-modal="partnership?format=<?= $key ?>">Залишити запит</button>
              </article>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередня пропозиція">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступна пропозиція">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     04 · Відео — постер відкриває ролик у GLightbox (той самий
     [data-glightbox], що галерея). TODO: відео корпоративного
     заняття від клієнта — поки стоїть ролик із головної.
     ============================================================ -->
<section class="section pt-0">
  <div class="container container--narrow">
    <div class="section-head">
      <h2 data-reveal="lines">Як проходять заняття для команди</h2>
    </div>

    <a class="video-card" href="assets/video/hero.mp4" data-glightbox data-reveal
       aria-label="Дивитись відео про заняття для команди">
      <img src="assets/img/gallery/6.jpg" alt="Заняття в залі студії" loading="lazy">
      <span class="btn btn--icon btn--filled video-card__play" aria-hidden="true">
        <svg class="icon"><use href="assets/icons/sprite.svg#icon-play"></use></svg>
      </span>
    </a>
  </div>
</section>

<!-- ============================================================
     05 · Відгуки партнерів — друге мохове поле (третє — футер)
     ============================================================ -->
<?php
$reviews_items = $reviews;
$reviews_title = 'Що кажуть партнери';
include 'partials/reviews.php';
?>

<!-- ============================================================
     06 · Запит — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Розкажіть, що потрібно';
$cta_text  = 'Напишіть формат, приблизну кількість людей і терміни — відповімо, чи можемо взяти, і скільки це коштує.';
$cta_modal = 'partnership';
$cta_btn   = 'Залишити запит';
$cta_link  = ['Подзвонити', 'tel:+380630150517'];
include 'partials/cta.php';
?>

<?php
$seo_title = 'Співпраця зі студією «Пілатес Львів»';
$seo_text  = <<<HTML
  <p>
    Студія «Пілатес Львів» працює з компаніями, тренерами, лікарями та
    брендами. Корпоративні заняття проводимо у власних залах із Cadillac,
    Reformer і Wall Unit або виїздом в офіс, а тренерам здаємо зал погодинно
    для роботи з власними клієнтами. Лікарям і фізіотерапевтам допомагаємо
    вести пацієнтів після травм за протоколом. Опишіть свій запит — підберемо
    формат і локацію.
  </p>
HTML;

include 'partials/footer.php';
?>
