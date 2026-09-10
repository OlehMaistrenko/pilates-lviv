  </main>

  <?php
  // $contact приходить із header.php (він завжди включається першим).
  // Фолбек — щоб футер не падав, якщо колись знадобиться окремо.
  $contact = $contact ?? [
    'phone'      => '+38 (063) 015-05-17',
    'phone_href' => 'tel:+380630150517',
    'address'    => 'м. Львів, вул. Б. Романицького, 24а',
    'map'        => '#',
    'instagram'  => '#',
    'facebook'   => '#',
  ];

  // Повна мапа сайту. Хедер показує 7 пунктів; сюди виходять усі розділи,
  // включно з тими, яких у хедері немає (кабінет, політика, галерея).
  $sitemap = [
    'Тренування' => [
      ['Пілатес Springtone', 'training-pilates.php'],
      ['Йога', 'training-yoga.php'],
      ['Функціональне відновлення', 'training-recovery.php'],
      ['Танці', 'training-dance.php'],
      ['Консультація фізіолога', 'training-physio.php'],
      ['Навчальний центр', 'academy.php'],
    ],
    'Студія' => [
      ['Про студію', 'about.php'],
      ['Наша команда', 'team.php'],
      ['Брюховичі', 'location-bryukhovychi.php'],
      ['Чупринки', 'location-chuprynky.php'],
      ['Сихів', 'location-sykhiv.php'],
      ['Події', 'events.php'],
    ],
    'Клієнтам' => [
      ['Розклад', 'schedule.php'],
      ['Ціни', 'prices.php'],
      ['Кабінет клієнта', 'account.php'],
      ['Питання та відповіді', 'faq.php'],
      ['Політика конфіденційності', 'privacy.php'],
    ],
    'Ще' => [
      ['Співпраця', 'partnership.php'],
      ['Блог', 'blog.php'],
      ['Контакти', 'contacts.php'],
    ],
  ];
  ?>

  <footer class="site-footer patterned on-dark">
    <?php if (!empty($seo_title) && !empty($seo_text)): ?>
      <div class="container">
        <!-- лінія живе на внутрішньому блоці, а не на .container: бордер
             контейнера ліг би на padding-box і був би довшим за розділювач
             під .site-footer__top на два гуттери -->
        <div class="footer-seo">
          <h2 class="footer-seo__title"><?= $seo_title ?></h2>
          <div class="simple-text text--muted footer-seo__text" id="footer-seo-text">
            <?= $seo_text ?>
          </div>
          <button type="button" class="btn btn--outlined btn--light btn--sm footer-seo__toggle" aria-expanded="false" aria-controls="footer-seo-text"
                  data-label-more="Читати більше" data-label-less="Читати менше">
            <span class="footer-seo__toggle-label">Читати більше</span>
          </button>
        </div>
      </div>
    <?php endif; ?>

    <div class="container">
      <div class="site-footer__top">
        <div class="site-footer__brand">
          <!-- золотий логотип на моховому полі — дозволений варіант із брендбуку -->
          <a class="brand brand--footer" href="index.php" aria-label="Пілатес Львів — на головну">
            <span class="brand__logo" role="img" aria-label="Pilates Lviv"></span>
          </a>

          <address class="site-footer__contacts">
            <a class="icon-link" href="<?= $contact['map'] ?>" target="_blank" rel="noopener">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
              <?= $contact['address'] ?>
            </a>
            <a class="icon-link" href="<?= $contact['phone_href'] ?>">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
              <?= $contact['phone'] ?>
            </a>
          </address>

          <button type="button" class="btn btn--outlined btn--light btn--sm" data-modal="callback">Замовити дзвінок</button>

          <div class="site-footer__social">
            <a class="btn btn--icon btn--sm btn--outlined btn--light" href="<?= $contact['instagram'] ?>" target="_blank" rel="noopener" aria-label="Instagram">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-instagram"></use></svg>
            </a>
            <a class="btn btn--icon btn--sm btn--outlined btn--light" href="<?= $contact['facebook'] ?>" target="_blank" rel="noopener" aria-label="Facebook">
              <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-facebook"></use></svg>
            </a>
          </div>
        </div>

        <nav class="site-footer__nav" aria-label="Мапа сайту">
          <?php foreach ($sitemap as $head => $links): ?>
            <div class="site-footer__col">
              <p class="site-footer__head"><?= $head ?></p>
              <ul>
                <?php foreach ($links as [$label, $href]): ?>
                  <li><a href="<?= $href ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>
        </nav>
      </div>

      <div class="site-footer__bottom">
        <!-- «est. 2015» повторювати тут не треба — воно вже є в самому логотипі -->
        <p class="text text--sm">© 2015–<?= date('Y') ?> Студія «Пілатес Львів»</p>
        <p class="text text--sm"><a href="privacy.php">Політика конфіденційності</a></p>
        <p class="site-footer__dev text text--sm">
          <a href="https://redstone.media/" target="_blank" rel="noopener" title="Розроблено RedStone">
            Developed by <img src="assets/logo/redstone.svg" alt="RedStone" width="93" height="14" loading="lazy">
          </a>
        </p>
      </div>
    </div>
  </footer>

  <!-- Спільний overlay для AJAX-модалок (info/help) — контент вантажиться в __content -->
  <div class="modal-overlay" id="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-overlay-title" data-lenis-prevent hidden>
    <div class="modal-overlay__backdrop" data-modal-close></div>
    <div class="modal-overlay__panel">
      <button type="button" class="btn btn--icon btn--sm btn--outlined modal-overlay__close" data-modal-close aria-label="Закрити">
        <svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-close"></use></svg>
      </button>
      <div class="modal-overlay__content" id="modal-overlay-content"></div>
    </div>
  </div>

  <!-- Deferred CSS: секції нижче першого екрана + повна стилізація модалок/
       мобільного меню. Вендорські стилі — перед цим лінком, за тією самою
       логікою. Swiper/Mapbox вантажаться лише там, де сторінка виставила
       $vendor_swiper/$vendor_map (до include цього партіалу). -->
  <?php if (!empty($vendor_swiper)): ?>
    <link rel="stylesheet" href="css/vendor/swiper/swiper-bundle.min.css">
  <?php endif; ?>
  <?php if (!empty($vendor_map)): ?>
    <link rel="stylesheet" href="css/vendor/mapbox/mapbox-gl.css">
  <?php endif; ?>
  <link rel="stylesheet" href="css/styles.css">
  <?php if (!empty($vendor_swiper)): ?>
    <script defer src="js/vendor/swiper/swiper-bundle.min.js"></script>
  <?php endif; ?>
  <?php if (!empty($vendor_map)): ?>
    <!-- Без defer, як swiper: main.js читає window.mapboxgl у момент свого
         запуску. Сама карта створюється ліниво (IntersectionObserver). -->
    <script defer src="js/vendor/mapbox/mapbox-gl.js"></script>
  <?php endif; ?>

  <!-- Lenis + GSAP на всіх сторінках без гейта: плавний скрол — частина
       відчуття сайту, а не фіча однієї секції -->
  <script defer src="js/vendor/lenis/lenis.min.js"></script>
  <script defer src="js/vendor/gsap/gsap.min.js"></script>
  <script defer src="js/vendor/gsap/ScrollTrigger.min.js"></script>

  <script defer src="js/main.js"></script>
</body>

</html>
