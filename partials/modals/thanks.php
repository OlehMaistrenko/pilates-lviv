<?php
/**
 * Модалка подяки — відкривається програмно (window._functions.loadModal)
 * після сабміту будь-якої .form (js/main.js), не через [data-modal].
 * Заголовок/текст можна передати query-параметрами title/text.
 */
$title = trim($_GET['title'] ?? '') ?: 'Дякуємо, ми вам передзвонимо';
$text  = trim($_GET['text'] ?? '') ?: 'Зазвичай протягом години в робочий час. Підберемо зручний час і відповімо на питання.';
?>
<div class="modal__head">
  <h2 class="modal__title" id="modal-overlay-title"><?= htmlspecialchars($title) ?></h2>
  <p class="text text--sm text--muted mt-3"><?= htmlspecialchars($text) ?></p>
</div>
