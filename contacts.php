<?php
$nav = 'contacts';
$page_title = 'Контакти — студія «Пілатес Львів»';
$page_description = 'Телефон, адреси трьох залів студії «Пілатес Львів» у Львові, форма зв’язку й карта з маршрутом до найближчої локації.';
$header_over_hero = true;   // банер темний — хедер лягає поверх нього прозорим

// Теми звернення для селекта у формі. Ключі збігаються з розділами сайту —
// у WP підуть у тему листа.
$subjects = [
  'booking'  => 'Запис на заняття',
  'prices'   => 'Абонементи й ціни',
  'academy'  => 'Навчальний центр',
  'partner'  => 'Співпраця',
  'other'    => 'Інше',
];

include 'partials/header.php';
?>

<!-- ============================================================
     01 · Банер — той самий .hero--page, що на team/locations
     ============================================================ -->
<section class="hero hero--page on-dark">
  <img class="hero__video" src="assets/img/gallery/7.jpg" alt="" aria-hidden="true"
       width="2400" height="1100" loading="eager">
  <div class="hero__tint" aria-hidden="true"></div>

  <div class="container container--full">
    <div class="hero__inner">
      <nav class="breadcrumbs breadcrumbs--rule text--sm" aria-label="Хлібні крихти">
        <a href="index.php">Головна</a>
        <span aria-hidden="true">·</span>
        <span aria-current="page">Контакти</span>
      </nav>

      <div class="hero__bottom">
        <h1 class="hero__title" data-reveal="lines">КОНТАКТИ</h1>

        <p class="text--lead hero__lead balance" data-reveal style="--reveal-i: 1">
          Один телефон на три зали. Подзвоніть або залиште запит —
          передзвонимо й підберемо час.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     02 · Звʼязок — контакти ліворуч, форма праворуч. Адрес тут немає
     навмисно: усі три зали з адресами й маршрутом — у карті нижче
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="contact__grid">
      <div class="contact__aside" data-reveal>
        <h2>Напишіть або зателефонуйте</h2>

        <p class="text--muted mt-5">
          Відповідаємо в робочий час. Якщо питання про запис — скажіть, коли
          вам зручно, і ми одразу назвемо вільні вікна на найближчій локації.
        </p>

        <address class="contact__contacts mt-6">
          <a class="icon-link" href="<?= $contact['phone_href'] ?>">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
            <?= $contact['phone'] ?>
          </a>
        </address>

        <div class="contact__social mt-5">
          <a class="btn btn--icon btn--sm btn--outlined" href="<?= $contact['instagram'] ?>" target="_blank" rel="noopener" aria-label="Instagram">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-instagram"></use></svg>
          </a>
          <a class="btn btn--icon btn--sm btn--outlined" href="<?= $contact['facebook'] ?>" target="_blank" rel="noopener" aria-label="Facebook">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-facebook"></use></svg>
          </a>
        </div>

        <p class="text--sm text--muted mt-6">
          Питання про заняття, абонементи чи курс — <a href="faq.php">у відповідях на часті питання</a>.
        </p>
      </div>

      <!-- Перша інлайн-форма на сайті: решта живе в модалках. Маска телефону
           (IMask) і SlimSelect на селекті — з js/main.js, він проходить по
           всьому документу; сабміт перехоплює той самий обробник .form
           і відкриває модалку подяки. Бекенду ще немає — action порожній. -->
      <form class="form" action="" method="post" data-reveal style="--reveal-i: 1">
        <div class="form__row">
          <label class="form__label" for="contact-name">Імʼя</label>
          <input class="form__input" type="text" id="contact-name" name="name" autocomplete="name"
                 placeholder="Ірина" required>
        </div>

        <div class="form__row">
          <label class="form__label" for="contact-phone">Телефон</label>
          <input class="form__input" type="tel" id="contact-phone" name="phone" autocomplete="tel"
                 placeholder="+380 63 015 05 17" required>
        </div>

        <div class="form__row">
          <label class="form__label" for="contact-subject">Тема</label>
          <select class="form__input" id="contact-subject" name="subject" data-slimselect>
            <?php foreach ($subjects as $key => $label): ?>
              <option value="<?= $key ?>"><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form__row">
          <label class="form__label" for="contact-note">Повідомлення</label>
          <textarea class="form__textarea" id="contact-note" name="note" rows="4"
                    placeholder="Коли вам зручно займатись і чи є травми, про які варто знати"></textarea>
        </div>

        <div class="contact__submit">
          <button type="submit" class="btn btn--filled">Надіслати</button>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- ============================================================
     03 · Карта — три зали, «де я», маршрут (partials/map.php;
     $vendor_map виставляє сам партіал, коли є токен)
     ============================================================ -->
<?php include 'partials/map.php'; ?>

<!-- ============================================================
     04 · Записатись — єдине умброве поле сторінки («двері»)
     ============================================================ -->
<?php
$cta_title = 'Записатись на заняття';
$cta_text  = 'Перше заняття — знайомство: тренер подивиться, як ви рухаєтесь, і підбере формат.';
include 'partials/cta.php';
?>

<?php
$seo_title = 'Контакти студії «Пілатес Львів»';
$seo_text  = <<<HTML
  <p>
    Студія «Пілатес Львів» працює з 2015 року на трьох локаціях у Львові:
    Чупринки, Брюховичі та Сихів. Телефон один на всі зали —
    {$contact['phone']}; записатись можна дзвінком, через форму на цій
    сторінці або в дірект Instagram.
  </p>
  <p>
    На кожній локації — Cadillac, Reformer, Wall Unit і Wunda Chair, а
    абонемент діє в усіх трьох залах. Якщо не знаєте, який зал ближчий,
    скористайтесь картою вище: вона покаже маршрут пішки чи автом від
    вашої позиції.
  </p>
HTML;

include 'partials/footer.php';
?>
