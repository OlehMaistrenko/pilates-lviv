<?php
/**
 * Службова сторінка розробника — .simple-text з усіма підтримуваними
 * тегами одразу. НЕ частина сайту: ніде не лінкована (крім dev-pages.php),
 * у $sitemap футера не входить.
 *
 * Сюди приходять перевіряти, як CMS-текст поводиться в найгіршому випадку:
 * підряд заголовки, довга таблиця, вкладені списки, кадр, відео, галерея.
 * Текст навмисно змістовий, а не «lorem» — довжина рядка й переноси
 * кирилиці впливають на верстку.
 */
$nav = '';
$page_title = 'simple-text — dev';
$page_description = '';
$vendor_swiper = true;    // галерея-слайдер усередині тексту
$vendor_lightbox = true;  // клік по кадру галереї

include 'partials/header.php';
?>

<div class="post-header">
  <div class="container">
    <h1 class="post-header__title">simple-text — усі теги</h1>
    <p class="text--sm text--muted mt-5">
      p · h2 · h3 · h4 · ul · ol · li · blockquote · cite · figure ·
      figcaption · img · table · hr · strong · em · a · video · iframe
    </p>
  </div>
</div>

<section class="section">
  <div class="container container--narrow">
    <div class="simple-text">
      <p class="text--lead">
        Лід-абзац через <em>.text--lead</em> — так його ставить редактор у
        WP, коли перший абзац треба зробити більшим. Далі йде звичайний
        текст без жодних класів: усе, що нижче, — голі теги з CMS.
      </p>

      <h2>Заголовок другого рівня</h2>
      <p>
        Звичайний абзац із <strong>жирним фрагментом</strong>,
        <em>курсивом із редактора</em> і
        <a href="prices.php">посиланням на іншу сторінку</a>. Речення
        навмисно довге, щоб у вузькому контейнері воно перенеслось
        щонайменше на три рядки і було видно міжрядковий інтервал.
      </p>

      <h3>Заголовок третього рівня одразу під абзацом</h3>
      <p>
        Короткий абзац на один рядок із <b>b</b> та <i>i</i> — те, що
        кнопки редактора ставлять замість strong/em при вставці з Word.
      </p>

      <h4>Заголовок четвертого рівня</h4>
      <p>
        Перевірка найгіршого випадку: h3 і h4 стоять підряд, без тексту
        між ними.
      </p>

      <h3>Ще один h3</h3>
      <h4>І h4 одразу за ним</h4>

      <ul>
        <li>Маркований список — короткий пункт</li>
        <li>
          Довгий пункт, який точно перенесеться на два рядки: важливо
          бачити, чи другий рядок вирівняний по тексту, а не по маркеру
        </li>
        <li>
          Пункт із вкладеним списком
          <ul>
            <li>Вкладений рівень — один</li>
            <li>Вкладений рівень — два</li>
          </ul>
        </li>
      </ul>

      <ol>
        <li>Нумерований список — перший крок</li>
        <li>Другий крок</li>
        <li>
          Третій крок із вкладеною нумерацією
          <ol>
            <li>Підкрок</li>
            <li>
              Ще підкрок, уже з третім рівнем
              <ol>
                <li>Найглибший рівень</li>
                <li>І ще один</li>
              </ol>
            </li>
          </ol>
        </li>
      </ol>

      <blockquote>
        Коли мій клієнт каже, що в нього більше не болить спина, — це
        найбільша втіха для мене.
        <cite>Галина, тренерка студії</cite>
      </blockquote>

      <blockquote>
        Цитата без підпису — щоб перевірити відступ знизу, коли cite немає.
      </blockquote>

      <hr>

      <figure>
        <img src="assets/img/location-1/1.jpg" alt="Зала студії з тренажерами" loading="lazy">
        <figcaption>Підпис до кадру — figcaption під зображенням</figcaption>
      </figure>

      <p>Зображення без figure, просто img у потоці тексту:</p>
      <img src="assets/img/gallery/1.jpg" alt="Кадр із зали студії" loading="lazy">

      <h2>Таблиця — тільки в обгортці</h2>
      <p>
        Обовʼязково <em>.simple-text__table-wrap</em>: інакше широка
        таблиця розпирає сторінку замість власного горизонтального скролу.
      </p>
      <div class="simple-text__table-wrap">
        <table>
          <thead>
            <tr>
              <th>Формат</th>
              <th>Тривалість</th>
              <th>Людей у групі</th>
              <th>Обладнання</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>Ознайомче</th>
              <td>60 хв</td>
              <td>1</td>
              <td>Reformer</td>
            </tr>
            <tr>
              <th>Персональне</th>
              <td>55 хв</td>
              <td>1</td>
              <td>Reformer, Cadillac, Wunda Chair</td>
            </tr>
            <tr>
              <th>Спліт</th>
              <td>55 хв</td>
              <td>2</td>
              <td>Reformer, Cadillac</td>
            </tr>
            <tr>
              <th>Групове</th>
              <td>55 хв</td>
              <td>до 6</td>
              <td>Мат, Barrel</td>
            </tr>
          </tbody>
        </table>
      </div>

      <h2>Відео</h2>
      <p>Вбудований плеєр — голий iframe, без обгортки:</p>
      <iframe src="https://www.youtube.com/embed/MvjMk6BaMtc" title="Заняття на Reformer"
              loading="lazy" allowfullscreen></iframe>

      <p>Власний файл — тег video:</p>
      <video src="assets/video/hero.mp4" controls muted playsinline preload="none"
             poster="assets/img/hero-poster.jpg"></video>

      <h2>Галерея-слайдер усередині тексту</h2>
      <div class="simple-text__gallery-wrap">
        <div class="swiper-wrap" data-reveal>
          <div class="swiper" data-swiper='{"spaceBetween":16}'>
            <div class="swiper-wrapper">
              <?php foreach ([2, 3, 4, 5, 6, 7] as $i): ?>
                <figure class="swiper-slide">
                  <a class="gallery__zoom" href="assets/img/location-1/<?= $i ?>.jpg" data-glightbox data-gallery="dev"
                     aria-label="Відкрити фото на весь екран">
                    <img src="assets/img/location-1/<?= $i ?>.jpg" alt="Кадр із зали студії" loading="lazy">
                  </a>
                </figure>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="slider-controls mt-5">
            <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попереднє фото">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
            </button>
            <div class="swiper-pagination"></div>
            <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступне фото">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
            </button>
          </div>
        </div>
      </div>

      <hr>

      <h2>Останній блок</h2>
      <p>
        Абзац наприкінці — щоб було видно, що нижній відступ блоку дає
        секція, а не сам текст.
      </p>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
