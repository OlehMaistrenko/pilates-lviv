<?php
require_once __DIR__ . '/api/prices.php';

$nav = 'prices';
$page_title = 'Ціни та абонементи — студія «Пілатес Львів»';
$page_description = 'Абонементи на пілатес-мат, реформер, кульки BALLance та персональні заняття: кількість занять, термін дії й умови заморозки — по кожному тарифу.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим
$vendor_swiper = true;      // тарифи — слайдер карток

$prices = prices_query();

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий .hero--page, що на trainings/schedule
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/gallery/3.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Ціни</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">ЦІНИ</h1>

        <p class="text--lead hero__lead" data-reveal style="--reveal-i: 1">
          Абонемент діє 30 днів від першого заняття. Можна заморозити на
          час відпустки чи хвороби — умови вказані по кожному тарифу.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Тарифи — таби за групою тренувань, картки абонементів
     ============================================================ -->
<section class="section section--tight-top">
  <div class="container">
    <?php if (!$prices['results']): ?>
      <p class="text--lead text--muted">
        Тарифи зараз оновлюються. Зателефонуйте — підкажемо ціну.
      </p>
    <?php else: ?>
      <div data-tabs>
        <div class="filters mb-7" role="tablist" aria-label="Групи тарифів">
          <?php foreach ($prices['results'] as $i => $tab): ?>
            <button type="button"
                    class="btn btn--sm btn--tab<?= $i === 0 ? ' is-current' : '' ?>"
                    role="tab"
                    aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                    aria-controls="tariffs-<?= $tab['slug'] ?>"
                    id="tab-<?= $tab['slug'] ?>"
                    data-tab-btn="<?= $tab['slug'] ?>"><?= htmlspecialchars($tab['title']) ?></button>
          <?php endforeach; ?>
        </div>

        <div data-tabs-viewport>
          <?php foreach ($prices['results'] as $i => $tab): ?>
            <div class="tariffs swiper-wrap"
                 role="tabpanel"
                 id="tariffs-<?= $tab['slug'] ?>"
                 aria-labelledby="tab-<?= $tab['slug'] ?>"
                 data-tab-panel="<?= $tab['slug'] ?>"
                 <?= $i === 0 ? '' : 'hidden' ?>>
              <div class="swiper" data-swiper='{"slidesPerView":1.15,"spaceBetween":16,"breakpoints":{"769":{"slidesPerView":2.3,"spaceBetween":24},"1081":{"slidesPerView":3,"spaceBetween":24}}}'>
                <ul class="swiper-wrapper">
                  <?php foreach ($tab['items'] as $j => $t): ?>
                    <li class="swiper-slide">
                      <article class="tariff" data-reveal style="--reveal-i: <?= $j % 4 ?>">
                        <div class="tariff__head">
                          <h2 class="tariff__title"><?= htmlspecialchars($t['title']) ?></h2>
                          <?php if ($t['subtitle']): ?>
                            <p class="text--sm text--muted mt-2"><?= htmlspecialchars($t['subtitle']) ?></p>
                          <?php endif; ?>
                        </div>

                        <?php if ($t['rows']): ?>
                          <ul class="rows tariff__rows">
                            <?php foreach ($t['rows'] as [$label, $value]): ?>
                              <li class="rows__item rows__item--tariff">
                                <span class="label text--muted"><?= $label ?></span>
                                <span class="text--sm"><?= $value ?></span>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                        <?php endif; ?>

                        <?php if ($t['price'] !== null): ?>
                          <p class="tariff__price"><?= number_format((float)$t['price'], 2, '.', ' ') ?> ₴</p>
                        <?php endif; ?>

                        <button type="button" class="btn btn--filled btn--block tariff__btn"
                                data-modal="callback?tariff=<?= rawurlencode($t['title']) ?>">Купити</button>
                      </article>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="slider-controls mt-5">
                <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередній тариф">
                  <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
                </button>
                <div class="swiper-pagination"></div>
                <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступний тариф">
                  <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ============================================================
     03 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Не знаєте, який абонемент обрати';
$cta_text  = 'Подзвоніть — спитаємо, який формат вам підходить, і підберемо тариф.';
include 'partials/cta.php';
?>

<?php
$faq_items = [
  ['Коли починається термін дії абонемента?',
   'З дня першого заняття, а не з дня оплати. Якщо купуєте заздалегідь, термін не витрачається, поки ви не прийшли вперше.'],
  ['Що таке заморозка абонемента?',
   'Пауза на визначену кількість днів — термін дії відсувається на цей час. Кількість заморозок і їх тривалість обмежені й залежать від тарифу.'],
  ['Абонемент можна передати іншій людині?',
   'Ні, абонемент іменний. Але кількість дозволених скасувань запису вказана в тарифі — заняття, яке скасували вчасно, не згорає.'],
  ['Що як заняття скасували з боку студії?',
   'Заняття повертається на баланс абонемента незалежно від ліміту скасувань — це не ваше рішення пропустити.'],
];
$faq_title = 'Питання про абонементи';
$faq_group = 'prices-faq';
include 'partials/faq.php';
?>

<?php
$seo_title = 'Ціни на абонементи в студії «Пілатес Львів»';
$seo_text  = <<<HTML
  <p>
    Абонементи діють 30 днів і рахуються з дня першого заняття. У кожного
    тарифу — своя кількість занять, кількість дозволених заморозок і їх
    тривалість. Разові заняття доступні окремо, без абонемента.
  </p>
  <p>
    Ціни діють на всіх трьох локаціях — Чупринки, Брюховичі, Сихів.
    Якщо не знаєте, який формат і яка кількість занять вам підійде —
    зателефонуйте, підкажемо з чого почати.
  </p>
HTML;

include 'partials/footer.php';
?>
