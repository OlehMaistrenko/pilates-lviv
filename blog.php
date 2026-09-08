<?php
$nav = 'blog';   // ключ із дітей $primary['about'] — батько дістає .is-active
$page_title = 'Блог — Студія «Пілатес Львів»';
$page_description = 'Як влаштовані заняття на Cadillac і Reformer, що брати на перше заняття, що робити зі спиною. Тексти тренерів студії.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим

// TODO: тимчасові тексти під верстку. Кадри — з наявних зйомок студії,
// підібрані за змістом; окремої зйомки під блог поки немає.
// [заголовок, slug, лід, [datetime, підпис], рубрика, кадр]
$posts = [
  ['Чим пілатес на реформері відрізняється від пілатесу на матах', 'reformer-vs-mat',
   'Пружина тримає й одночасно опирається — тому вправа, яка на маті не виходить, на реформері виходить із першого разу. Розбираємо, кому з чого починати.',
   ['2025-03-12', '12 березня 2025'], 'Пілатес', 'gallery/1.jpg'],

  ['Що взяти на перше заняття', 'first-class',
   'Одяг, шкарпетки, вода, час на дорогу. Коротко про те, що питають найчастіше перед першим приходом.',
   ['2025-02-26', '26 лютого 2025'], 'Практика', 'directions/1.jpeg'],

  ['Болить спина за столом: три вправи, які можна робити на роботі', 'back-at-desk',
   'Не заміна заняттю, але знімає напругу між зустрічами. Показує Галина.',
   ['2025-02-05', '5 лютого 2025'], 'Здоров’я спини', 'gallery/5.jpg'],

  ['Cadillac: навіщо студії тренажер, схожий на ліжко з рамою', 'cadillac',
   'Витягнення хребта у висі, робота лежачи, точне дозування навантаження — що на ньому реально роблять і кому він потрібен.',
   ['2025-01-21', '21 січня 2025'], 'Обладнання', 'gallery/2.jpg'],

  ['Скільки разів на тиждень ходити, щоб була різниця', 'how-often',
   'Двічі на тиждень — робочий мінімум. Що змінюється на 10, 20 і 30 занятті.',
   ['2025-01-09', '9 січня 2025'], 'Практика', 'gallery/4.jpg'],

  ['Пілатес після пологів: коли можна починати', 'postnatal',
   'Що каже лікар, з чого починаємо ми і чому перші заняття — індивідуальні.',
   ['2024-12-18', '18 грудня 2024'], 'Відновлення', 'directions/4.jpeg'],

  ['Springtone: що це за метод і чому ми працюємо саме за ним', 'springtone',
   'Пружинний опір замість вільної ваги. Як метод влаштований і чим відрізняється від класичного пілатесу.',
   ['2024-12-03', '3 грудня 2024'], 'Пілатес', 'gallery/3.jpg'],

  ['Як обрати студію пілатесу: на що дивитись, крім ціни', 'choose-studio',
   'Обладнання, розмір групи, освіта тренера. Чотири речі, які варто спитати перед абонементом.',
   ['2024-11-15', '15 листопада 2024'], 'Практика', 'gallery/6.jpg'],
];

// Кирилиця в ?rubric= перетворюється на %D0%BF%D1%96… — нечитабельно
// й ламається при копіюванні, тому ключ URL транслітеруємо в латиницю.
function blog_slug(string $s): string {
  $map = ['а'=>'a','б'=>'b','в'=>'v','г'=>'h','ґ'=>'g','д'=>'d','е'=>'e','є'=>'ie',
    'ж'=>'zh','з'=>'z','и'=>'y','і'=>'i','ї'=>'i','й'=>'i','к'=>'k','л'=>'l','м'=>'m',
    'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'kh',
    'ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'shch','ю'=>'iu','я'=>'ia','ь'=>'','’'=>'',
    ' '=>'-'];
  return strtr(mb_strtolower($s), $map);
}

// Рубрики рахуємо з самих статей, а не окремим списком: інакше додана
// стаття з новою рубрикою мовчки випала б із фільтра.
$rubrics = [];
foreach ($posts as $p) {
  $slug = blog_slug($p[4]);
  $rubrics[$slug] = ['label' => $p[4], 'count' => ($rubrics[$slug]['count'] ?? 0) + 1];
}

$active = $_GET['rubric'] ?? '';
if (!isset($rubrics[$active])) $active = '';   // невідома рубрика в URL — показуємо всі
$visible = $active === ''
  ? $posts
  : array_values(array_filter($posts, fn($p) => $p[4] === $rubrics[$active]['label']));

// Відфільтрована добірка — окрема адреса, тож і title у неї свій
if ($active !== '') {
  $page_title = $rubrics[$active]['label'] . ' — блог студії «Пілатес Львів»';
}

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий герой, що на головній, але не на весь екран
     (.hero--inner): під ним одразу список статей. Замість відео — кадр:
     заради шапки списку окреме відео не вантажимо.
     ============================================================ -->
<section class="hero hero--inner">
  <!-- Панорамний кадр (2.49:1) — під пропорції банера. Постать ліворуч,
       праворуч порожня стіна: лід лягає на чисту площину, а не на тіло.
       Окремий файл, а не path/2.jpeg: оригінал 2.7 МБ, а це перший
       eager-кадр сторінки — тут він важить 0.3 МБ, як і cta.jpg. -->
  <img class="hero__video" src="assets/img/blog-banner.jpg" alt="" aria-hidden="true"
       width="2400" height="964" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Блог</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">БЛОГ</h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Тексти тренерів: як влаштовані заняття, що робити зі спиною
          і на що дивитись, коли обираєш студію.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Список статей — три в ряд, пагінація під сіткою
     ============================================================ -->
<section class="section">
  <div class="container">
    <!-- Звичайні посилання, не таби: фільтр лишається в URL, тож рубрику
         можна відкрити напряму, зберегти й проіндексувати. JS не потрібен. -->
    <nav class="filters" aria-label="Рубрики блогу">
      <a class="filters__item<?= $active === '' ? ' is-current' : '' ?>"
         href="blog.php"<?= $active === '' ? ' aria-current="page"' : '' ?>>Усі</a>
      <?php foreach ($rubrics as $slug => $r): ?>
        <a class="filters__item<?= $active === $slug ? ' is-current' : '' ?>"
           href="blog.php?rubric=<?= $slug ?>"<?= $active === $slug ? ' aria-current="page"' : '' ?>>
          <?= $r['label'] ?>
          <span class="filters__count"><?= $r['count'] ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <ul class="posts">
      <?php foreach ($visible as $i => [$title, $slug, $excerpt, $date, $rubric, $img]): ?>
        <li class="post" data-reveal style="--reveal-i: <?= $i % 3 ?>">
          <a class="post__link" href="blog-single.php?post=<?= $slug ?>">
            <!-- alt порожній навмисно: назва статті вже є текстом самого
                 посилання, дубль змусив би скрінрідер прочитати її двічі -->
            <span class="post__media">
              <img src="assets/img/<?= $img ?>" alt="" width="1600" height="1000" loading="<?= $i < 3 ? 'eager' : 'lazy' ?>">
            </span>
            <span class="post__meta text--xs text--muted">
              <time datetime="<?= $date[0] ?>"><?= $date[1] ?></time>
              <span class="label post__rubric"><?= $rubric ?></span>
            </span>
            <span class="post__title"><?= $title ?></span>
            <span class="post__excerpt text--sm text--muted"><?= $excerpt ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Статична розмітка: сторінок поки одна, ?p=N обробить CMS.
         В окремій рубриці статей на другу сторінку не набирається. -->
    <?php if ($active === ''): ?>
      <nav class="pagination" aria-label="Сторінки блогу">
        <span class="pagination__page is-current" aria-current="page">1</span>
        <a class="pagination__page" href="blog.php?p=2">2</a>
        <a class="pagination__page" href="blog.php?p=3">3</a>
        <a class="pagination__next link-arrow" href="blog.php?p=2">
          Далі
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </a>
      </nav>
    <?php endif; ?>
  </div>
</section>

<!-- ============================================================
     03 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<section class="split split--booking split--cta">
  <div class="split__grid embossed">
    <figure class="split__media">
      <img class="split__layer" src="assets/img/cta.jpg" alt="Групове заняття в залі студії"
           width="2048" height="1365" loading="lazy"
           data-anim="parallax" data-parallax="10">
    </figure>

    <div class="split__body" data-anim="parallax" data-parallax="4">
      <h2 data-reveal="lines">Прочитали — приходьте спробувати</h2>
      <p class="text--lead split__lead" data-reveal>
        Перше заняття — знайомство: подивитись зал, поговорити з тренером
        і спробувати тренажери.
      </p>

      <div class="split__actions" data-reveal>
        <button type="button" class="btn btn--sand" data-modal="booking">Записатись</button>
        <a class="btn btn--ghost" href="schedule.php">Розклад занять</a>
      </div>
    </div>
  </div>
</section>

<?php
$seo_title = 'Блог студії «Пілатес Львів»';
$seo_text  = <<<HTML
  <p>
    Блог веде студія: тексти пишуть ті самі тренери, які ведуть заняття
    на Романицького. Тут розбираємо те, що найчастіше питають у залі —
    чим реформер відрізняється від килимка, коли можна повертатись до
    занять після пологів, скільки разів на тиждень ходити, щоб зміни
    були помітні.
  </p>
  <p>
    Тексти не замінюють консультацію: якщо є діагноз або біль, з яким
    ви приходите, про це краще поговорити з тренером до першого заняття.
    Питання, на які тут немає відповіді, ставте телефоном — відповідаємо
    самі, без ботів.
  </p>
HTML;

include 'partials/footer.php';
?>
