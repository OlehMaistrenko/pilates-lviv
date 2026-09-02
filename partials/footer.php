  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="site-footer__top">
        <div class="site-footer__brand">
          <p class="label">Site</p>
        </div>

        <nav class="site-footer__nav" aria-label="Footer navigation">
          <div class="site-footer__col">
            <p class="site-footer__head">Column 1</p>
            <ul>
              <li><a href="page-1.php">Page 1</a></li>
              <li><a href="page-2.php">Page 2</a></li>
            </ul>
          </div>
          <div class="site-footer__col">
            <p class="site-footer__head">Column 2</p>
            <ul>
              <li><a href="page-3.php">Page 3</a></li>
            </ul>
          </div>
        </nav>
      </div>

      <div class="site-footer__bottom">
        <p class="text text--sm">© <?= date('Y') ?> Site. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Спільний overlay для AJAX-модалок (info/help) — контент вантажиться в __content -->
  <div class="modal-overlay" id="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-overlay-title" data-lenis-prevent hidden>
    <div class="modal-overlay__backdrop" data-modal-close></div>
    <div class="modal-overlay__panel">
      <button type="button" class="btn-icon btn-icon--sm modal-overlay__close" data-modal-close aria-label="Close">
        <svg class="icon" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-close"></use></svg>
      </button>
      <div class="modal-overlay__content" id="modal-overlay-content"></div>
    </div>
  </div>

  <!-- Deferred CSS: секції нижче першого екрана + повна стилізація модалок/
       мобільного меню/пошуку. Вендорські стилі — перед цим лінком, за тією
       самою логікою. Swiper/Mapbox вантажаться лише там, де сторінка
       виставила $vendor_swiper/$vendor_map (до include цього партіалу). -->
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

  <?php if (!empty($vendor_motion)): ?>
    <script defer src="js/vendor/lenis/lenis.min.js"></script>
    <script defer src="js/vendor/gsap/gsap.min.js"></script>
    <script defer src="js/vendor/gsap/ScrollTrigger.min.js"></script>
  <?php endif; ?>

  <script defer src="js/main.js"></script>
</body>

</html>
