<?php
$nav = 'blog';
$page_title = 'Reformer vs Mat: в чому різниця — блог студії «Пілатес Львів»';
$page_description = 'Пружина тримає й одночасно опирається — тому вправа, яка на маті не виходить, на реформері виходить із першого разу. Розбираємо, кому з чого починати.';
$vendor_swiper = true;   // галерея фото всередині статті

// Приклад-демо детальної сторінки статті: два варіанти зверстані одним
// шаблоном через прапорець, а не окремими файлами.
// true  — банер-фото на всю ширину над заголовком, хедер прозорий і лягає
//         поверх нього (як на blog.php)
// false — сторінка одразу починається з заголовка, без банера — тоді
//         хедер звичайний, бежевий, з .header-spacer
$has_cover = true;
$header_over_hero = $has_cover;

include 'partials/header.php';
?>

<?php if ($has_cover): ?>
  <!-- ============================================================
       01 · Банер — той самий .hero--inner, що на blog.php, тільки без
       заголовка/ліда: тут вони йдуть окремим блоком нижче (02).
       ============================================================ -->
  <section class="hero hero--inner">
    <img class="hero__video" src="assets/img/gallery/1.jpg" alt="" aria-hidden="true"
         width="2400" height="1100" loading="eager">
    <div class="hero__tint" aria-hidden="true"></div>

    <div class="container container--full">
      <div class="hero__inner hero__inner--end">
        <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
          <a href="index.php">Головна</a>
          <span aria-hidden="true">·</span>
          <a href="blog.php">Блог</a>
          <span aria-hidden="true">·</span>
          <span aria-current="page">Reformer vs Mat</span>
        </nav>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- ============================================================
     02 · Заголовок і дата публікації
     ============================================================ -->
<section class="section pb-0">
  <div class="container container--narrow">
    <?php if (!$has_cover): ?>
      <nav class="breadcrumbs text--sm mb-5" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <a href="blog.php">Блог</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Reformer vs Mat</span>
      </nav>
    <?php endif; ?>

    <h1 class="post-header__title" data-reveal="lines">Чим пілатес на реформері відрізняється від пілатесу на матах</h1>

    <div class="text--sm text--muted mt-5">
      <time datetime="2025-03-12">12 березня 2025</time>
      <span aria-hidden="true"> · </span>
      <span class="label">Пілатес</span>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Тіло статті — .simple-text, повний набір WP rich-text тегів
     ============================================================ -->
<section class="section">
  <div class="container container--narrow">
    <div class="simple-text">
      <p class="text--lead">
        Пружина тримає й одночасно опирається — тому вправа, яка на маті
        не виходить, на реформері виходить із першого разу. Різниця не в
        «складніше/легше», а в тому, звідки береться опір.
      </p>

      <h2>Звідки береться навантаження</h2>
      <p>
        На маті все навантаження — це вага власного тіла і контроль
        балансу. На <strong>Reformer</strong> навантаження задає система
        пружин під платформою: тягнеш — пружина опирається в обидва боки,
        і на розтягу, і на поверненні. Це змінює саму вправу, не тільки
        її складність.
      </p>

      <ul>
        <li>Мат — вправа тримається на власній вазі й техніці дихання</li>
        <li>Reformer — опір можна точно дозувати, від однієї пружини до чотирьох</li>
        <li>Cadillac — те саме, але з вертикальними рамами для витягнення хребта</li>
      </ul>

      <h3>Кому з чого починати</h3>
      <p>
        Якщо болить спина або є грижа — перше заняття краще на реформері:
        пружина не дає зірватись у різкий рух. Якщо мета — витривалість
        і контроль дихання без обладнання, починають з мата й переходять
        на тренажер пізніше.
      </p>

      <blockquote>
        Коли мій клієнт каже, що в нього більше не болить спина, — це
        найбільша втіха для мене.
        <cite>Галина, тренерка студії</cite>
      </blockquote>

      <figure>
        <img src="assets/img/directions/2.jpeg" alt="Вправа на реформері під наглядом тренера" loading="lazy">
        <figcaption>Пружинний опір дозволяє точно контролювати навантаження на кожному повторенні</figcaption>
      </figure>

      <h2>Порівняння коротко</h2>
      <div class="simple-text__table-wrap">
        <table>
          <thead>
            <tr>
              <th>Критерій</th>
              <th>Reformer</th>
              <th>Мат</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Джерело опору</td>
              <td>Пружини, дозується точно</td>
              <td>Вага власного тіла</td>
            </tr>
            <tr>
              <td>Для болю в спині</td>
              <td>Підходить з першого заняття</td>
              <td>Потребує підготовки</td>
            </tr>
            <tr>
              <td>Обладнання потрібне</td>
              <td>Так, тільки в студії</td>
              <td>Ні, мат можна вдома</td>
            </tr>
            <tr>
              <td>Швидкість прогресу</td>
              <td>Видно за 5–10 занять</td>
              <td>Видно за 15–20 занять</td>
            </tr>
          </tbody>
        </table>
      </div>

      <ol>
        <li>Записуєтесь на перше заняття — знайомство із залом і тренером</li>
        <li>Тренер оцінює поставу і підбирає кількість пружин</li>
        <li>Перші 3–4 заняття — індивідуально, далі можна в групу</li>
      </ol>

      <h2>Як це виглядає в залі</h2>
      <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Заняття на Reformer у студії Пілатес Львів"
              loading="lazy" allowfullscreen></iframe>

      <h2>Кадри із зали</h2>
      <div class="simple-text__gallery-wrap">
        <div class="swiper-wrap" data-reveal>
          <div class="swiper" data-swiper='{"spaceBetween":16}'>
            <div class="swiper-wrapper">
              <figure class="swiper-slide">
                <img src="assets/img/gallery/2.jpg" alt="Reformer у залі студії" loading="lazy">
              </figure>
              <figure class="swiper-slide">
                <img src="assets/img/gallery/3.jpg" alt="Cadillac у залі студії" loading="lazy">
              </figure>
              <figure class="swiper-slide">
                <img src="assets/img/gallery/4.jpg" alt="Групове заняття на матах" loading="lazy">
              </figure>
              <figure class="swiper-slide">
                <img src="assets/img/directions/3.jpeg" alt="Індивідуальне заняття з тренером" loading="lazy">
              </figure>
              <figure class="swiper-slide">
                <img src="assets/img/gallery/5.jpg" alt="Розтяжка на Cadillac" loading="lazy">
              </figure>
              <figure class="swiper-slide">
                <img src="assets/img/gallery/6.jpg" alt="Зал студії з тренажерами" loading="lazy">
              </figure>
              <figure class="swiper-slide">
                <img src="assets/img/directions/5.jpeg" alt="Вправа на Reformer з тренером" loading="lazy">
              </figure>
            </div>
          </div>
          <div class="slider-controls mt-5">
            <button type="button" class="btn-icon btn-icon--outlined swiper-prev" aria-label="Попереднє фото">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
            </button>
            <div class="swiper-pagination"></div>
            <button type="button" class="btn-icon btn-icon--outlined swiper-next" aria-label="Наступне фото">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
            </button>
          </div>
        </div>
      </div>

      <hr>

      <p>
        Однозначної відповіді «що краще» немає — <em>обидва формати</em>
        працюють на одну мету, просто різними засобами. Найточніше це
        визначить тренер на першому занятті. Розклад групових і
        індивідуальних занять — на <a href="schedule.php">сторінці розкладу</a>.
      </p>
    </div>
  </div>
</section>

<!-- ============================================================
     04 · Питання — акордеон, той самий патерн що на головній (FAQ)
     ============================================================ -->
<section class="section pt-0">
  <div class="container container--narrow">
    <h2 class="mb-5" data-reveal="lines">Питання про реформер і мат</h2>

    <div data-reveal>
      <?php
      $post_faq = [
        ['Чи можна одразу на реформер без досвіду?', 'Так, перше заняття завжди індивідуальне: тренер підбирає кількість пружин і рух під ваш рівень.'],
        ['Reformer замінює мат повністю?', 'Ні, багато груп поєднують обидва формати в межах одного заняття — тренер сам вирішує пропорцію.'],
        ['Скільки коштує заняття на реформері?', 'Тарифи на реформер і мат відрізняються — актуальні ціни на сторінці тарифів.'],
      ];
      foreach ($post_faq as $i => [$q, $a]): ?>
        <div class="accordion" data-accordion-group="post-faq">
          <div class="accordion__summary" role="button" tabindex="0" aria-expanded="false" aria-controls="post-faq-<?= $i ?>">
            <span class="accordion__title"><?= $q ?></span>
            <span class="accordion__icon" aria-hidden="true"></span>
          </div>
          <div class="accordion__body" id="post-faq-<?= $i ?>">
            <div class="accordion__body-inner">
              <p class="text--muted"><?= $a ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
