<?php
// Заголовок статусу — до будь-якого виводу, той самий принцип, що
// Cache-Control у account.php. Працює незалежно від того, чи веб-сервер
// сам націлений на цей файл як ErrorDocument (це вже налаштування деплою,
// не верстки).
http_response_code(404);

$nav = '';   // сторінки немає в $primary
$page_title = 'Сторінку не знайдено — студія «Пілатес Львів»';
$page_description = 'Такої сторінки більше немає або вона переїхала. Поверніться на головну або подивіться розклад занять.';

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Той самий .page-open, що на сторінці тренера й у кабінеті:
     сторінка починається без героя, .error-404 — свій набір
     title/text/actions за тим самим принципом, що .account-guest.
     ============================================================ -->
<section class="page-open page-open--full">
  <div class="container">
    <nav class="breadcrumbs breadcrumbs--rule text--sm mb-0" aria-label="Хлібні крихти">
      <a href="index.php">Головна</a>
      <span aria-hidden="true">·</span>
      <span aria-current="page">Сторінку не знайдено</span>
    </nav>

    <div class="error-404">
      <p class="error-404__code" aria-hidden="true">404</p>
      <h1 class="error-404__title" data-reveal>Сторінку не знайдено</h1>
      <p class="text--lead text--muted error-404__text" data-reveal style="--reveal-i: 1">
        Такої сторінки більше немає або адреса змінилась. Перевірте
        посилання або перейдіть на головну — звідти легко знайти потрібний
        розділ.
      </p>
      <div class="error-404__actions" data-reveal style="--reveal-i: 2">
        <a class="btn btn--filled" href="index.php">На головну</a>
        <a class="btn btn--outlined" href="schedule.php">Розклад занять</a>
      </div>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
