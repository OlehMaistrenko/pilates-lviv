<?php
/**
 * Звʼязок — контакти ліворуч, форма праворуч. Перед include (усе опційно):
 *   $form_title    — заголовок. Дефолт «Напишіть або зателефонуйте».
 *   $form_text     — абзац під заголовком.
 *   $form_phone / $form_phone_href — телефон. Дефолт — загальний із $contact.
 *   $form_subjects — [ключ => назва] для селекта «Тема». [] прибирає селект.
 *   $form_hidden   — [name => value] прихованих полів (напр. локація).
 *   $form_foot     — рядок під формою зліва, HTML. '' прибирає.
 *   $form_class    — додатковий клас на <section> (напр. 'pt-0').
 *
 * Адрес тут немає навмисно: і на contacts.php, і на детальній локації
 * адреса з маршрутом живе в карті поруч.
 *
 * Сабміт перехоплює глобальний обробник .form у js/main.js і відкриває
 * модалку подяки; бекенду ще немає — action порожній. Маска телефону
 * (IMask) і SlimSelect на селекті теж із main.js, він іде по всьому
 * документу, окремого гейта партіал не потребує.
 */
$form_title = $form_title ?? 'Напишіть або зателефонуйте';
$form_text  = $form_text  ?? 'Відповідаємо в робочий час. Якщо питання про запис — скажіть, коли вам зручно, і ми одразу назвемо вільні вікна на найближчій локації.';
$form_phone      = $form_phone      ?? $contact['phone'];
$form_phone_href = $form_phone_href ?? $contact['phone_href'];
// Ключі збігаються з розділами сайту — у WP підуть у тему листа.
$form_subjects = $form_subjects ?? [
  'booking'  => 'Запис на заняття',
  'prices'   => 'Абонементи й ціни',
  'academy'  => 'Навчальний центр',
  'partner'  => 'Співпраця',
  'other'    => 'Інше',
];
$form_hidden = $form_hidden ?? [];
$form_foot   = $form_foot   ?? 'Питання про заняття, абонементи чи курс — <a href="faq.php">у відповідях на часті питання</a>.';
$form_class  = $form_class  ?? '';
?>
<section class="section<?= $form_class ? ' ' . $form_class : '' ?>">
  <div class="container">
    <div class="contact__grid">
      <div class="contact__aside" data-reveal>
        <h2><?= $form_title ?></h2>

        <p class="text--muted mt-5"><?= $form_text ?></p>

        <address class="contact__contacts mt-6">
          <a class="icon-link" href="<?= $form_phone_href ?>">
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-phone"></use></svg>
            <?= htmlspecialchars($form_phone) ?>
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

        <?php if ($form_foot): ?>
          <p class="text--sm text--muted mt-6"><?= $form_foot ?></p>
        <?php endif; ?>
      </div>

      <form class="form" action="" method="post" data-reveal style="--reveal-i: 1">
        <?php foreach ($form_hidden as $name => $value): ?>
          <input type="hidden" name="<?= $name ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endforeach; ?>

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

        <?php if ($form_subjects): ?>
          <div class="form__row">
            <label class="form__label" for="contact-subject">Тема</label>
            <select class="form__input" id="contact-subject" name="subject" data-slimselect>
              <?php foreach ($form_subjects as $key => $label): ?>
                <option value="<?= $key ?>"><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

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
<?php unset($form_title, $form_text, $form_phone, $form_phone_href, $form_subjects, $form_hidden, $form_foot, $form_class); ?>
