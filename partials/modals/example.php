<?php $id = $_GET['id'] ?? null; ?>
<div class="modal__head">
  <h2 class="modal__title" id="modal-overlay-title">Modal title</h2>
</div>
<div class="modal__body">
  <div class="simple-text">
    <p>Приклад контенту модалки, вантажиться через AJAX (<code>partials/modals/loader.php?name=example</code>).
      Тригер — будь-який елемент з <code>data-modal="example"</code>.</p>
  </div>
  <?php if ($id): ?>
    <p class="text text--xs text--muted">ID: <?= htmlspecialchars($id) ?></p>
  <?php endif; ?>
</div>
