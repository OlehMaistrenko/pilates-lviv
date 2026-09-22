<?php
/**
 * Стос карток — галерея, де кадри лежать стосом, а передню картку можна
 * перетягнути вказівником: відпущена йде В КІНЕЦЬ стосу, решта зсувається
 * вперед. Напрямок жесту порядку не змінює — назад повертає тільки кнопка.
 *
 * Перед include задай:
 *   $stack_items — [[файл відносно assets/img/, alt], …]. Обовʼязково.
 *   $stack_title — заголовок секції; '' — секція без шапки.
 *   $stack_lead  — абзац праворуч у шапці. Опційно.
 *   $stack_link  — ['текст', 'href'] праворуч у шапці замість лід-абзацу
 *                  (напр. 3D-тур). Опційно, взаємовиключне з $stack_lead.
 *   $stack_class — додатковий клас на <section> (напр. 'pt-0'). Опційно.
 *
 * Swiper НЕ потрібен: блок самодостатній і працює на сторінці, яка не
 * виставила $vendor_swiper. Змінні скидаються в кінці — щоб два виклики
 * на одній сторінці не успадковували налаштування один одного.
 */
$stack_items = $stack_items ?? [];
$stack_title = $stack_title ?? 'Зали та обладнання';
$stack_lead  = $stack_lead  ?? null;
$stack_link  = $stack_link  ?? null;
$stack_class = $stack_class ?? '';

if ($stack_items):
  $stack_total = count($stack_items);
  // Скільки карток видно, рахуючи передню. Дублює --stack-depth у
  // .cardstack__frame (css/styles.css): PHP малює стос до першого кадру,
  // далі це саме число читає render() у main.js. Міняти треба в обох.
  $stack_depth = 4;
  // Мало фото — стос виглядав би дірявим (менше карток, ніж --stack-depth).
  // Добираємо колом до глибини стосу, а лічильник рахує $stack_total
  // (унікальні фото), а не кількість карток у DOM — data-photo нижче
  // несе номер оригіналу, JS читає його замість order[0].
  $stack_cards = $stack_items;
  while (count($stack_cards) < $stack_depth + $stack_depth / 2) {
    $stack_cards = array_merge($stack_cards, $stack_items);
  }
?>
<section class="section cardstack<?= $stack_class ? ' ' . $stack_class : '' ?>">
  <div class="container">
    <?php if ($stack_title): ?>
      <div class="section-head section-head--split">
        <h2 data-reveal="lines"><?= $stack_title ?></h2>
        <?php if ($stack_lead): ?>
          <p class="text--lead text--muted" data-reveal><?= $stack_lead ?></p>
        <?php elseif ($stack_link): ?>
          <a class="btn btn--outlined" href="<?= $stack_link[1] ?>" target="_blank" rel="noopener" data-reveal>
            <?= $stack_link[0] ?>
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="cardstack__wrap" data-cardstack data-reveal>
      <!-- role=group, а не список: це керована карусель без автоплею, і
           порядок карток у DOM навмисно не збігається з видимим -->
      <div class="cardstack__frame" role="group"
           aria-roledescription="галерея-стос"
           aria-label="Фото студії: стрілки — гортати, Enter — відкрити кадр на весь екран">
        <?php foreach ($stack_cards as $i => [$file, $alt]): ?>
          <!-- --i друкує PHP, а не JS: без скрипта стос усе одно
               намальований правильно, передня картка зверху. Глибші за
               видиму частину стосу одразу приховані — те саме, що потім
               рахує render() у main.js.
               data-photo — номер ОРИГІНАЛЬНОГО фото (0-based), не позиція
               в цьому (можливо задубльованому) списку: за нього тримається
               лічильник, щоб дублікати не накручували "01 / 02" далі "02". -->
          <figure class="cardstack__card<?= $i >= $stack_depth ? ' is-deep' : '' ?>"
                  style="--i: <?= min($i, $stack_depth - 1) ?>"
                  data-card data-photo="<?= $i % $stack_total ?>">
            <img src="assets/img/<?= $file ?>" alt="<?= htmlspecialchars($alt) ?>"
                 loading="lazy" draggable="false">
          </figure>
        <?php endforeach; ?>
      </div>

      <?php
        // Лічильник продубльований у розмітці, а не переставлений одним
        // елементом через CSS: десктопна версія стоїть окремо ліворуч від
        // кадру, мобільна — усередині .slider-controls, між кнопками
        // (--mobile/--desktop перемикає видимість медіа-запитом нижче).
        // aria-live лишається лише на одній копії: обидві озвучувати
        // скрінрідеру двічі те саме не треба. JS оновлює [data-stack-current]
        // на обох через querySelectorAll.
      ?>
      <p class="cardstack__counter cardstack__counter--desktop" aria-live="polite" aria-atomic="true">
        <span class="cardstack__num" data-stack-current>01</span><span class="cardstack__total">/ <?= str_pad($stack_total, 2, '0', STR_PAD_LEFT) ?></span>
      </p>

      <div class="slider-controls">
        <!-- .swiper-prev/.swiper-next — лише щоб .slider-controls дзеркалила
             стрілку; логіку JS чіпляє за data-stack-* -->
        <button type="button" class="btn btn--icon btn--outlined swiper-prev"
                data-stack-prev aria-label="Попереднє фото">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <p class="cardstack__counter cardstack__counter--mobile" aria-hidden="true">
          <span class="cardstack__num" data-stack-current>01</span><span class="cardstack__total">/ <?= str_pad($stack_total, 2, '0', STR_PAD_LEFT) ?></span>
        </p>
        <button type="button" class="btn btn--icon btn--outlined swiper-next"
                data-stack-next aria-label="Наступне фото">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>
<?php endif;

unset($stack_items, $stack_title, $stack_lead, $stack_link, $stack_class, $stack_total, $stack_depth, $stack_cards);
