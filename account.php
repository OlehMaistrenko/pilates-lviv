<?php
// Кабінет ніколи не віддається з кешу: інакше page cache покаже кабінет
// одного клієнта іншому (docs/ACCOUNT-WP.md §3 — «найнебезпечніша помилка
// в усьому проєкті»). Заголовок — до будь-якого виводу.
header('Cache-Control: no-store');

require_once __DIR__ . '/api/account.php';

$nav = '';   // у $primary ключа account немає — кабінет живе поза основним меню
$page_title = 'Кабінет клієнта — студія «Пілатес Львів»';
$page_description = 'Персональна інформація, баланс, абонементи та історія занять.';

$account_nav   = 'profile';
$account_title = 'Персональна інформація';

include 'partials/header.php';
?>

<?php include 'partials/account/layout-open.php'; ?>

<?php if (account_is_logged()): ?>
  <?php $profile = account_profile(); ?>

  <?php
  $user = account_user();
  $lim  = account_user_limits();
  ?>

  <!-- PATCH /user/ (PatchedUser) — усі поля опційні, летить лише те, що
       змінили. maxlength узято зі схеми, щоб браузер різав рядок ще до
       400 wrong_parameters. Пошта й телефон сюди НЕ входять: у них свої
       ендпоінти з підтвердженням (нижче). -->
  <form class="form account-form account-form--wide" action="" method="post" data-remote data-user="update">
    <div class="account-form__grid">
      <div class="form__row">
        <label class="form__label" for="user-first-name">Імʼя</label>
        <input class="form__input" type="text" id="user-first-name" name="first_name"
               autocomplete="given-name" maxlength="<?= $lim['first_name'] ?>"
               value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
      </div>
      <div class="form__row">
        <label class="form__label" for="user-last-name">Прізвище</label>
        <input class="form__input" type="text" id="user-last-name" name="last_name"
               autocomplete="family-name" maxlength="<?= $lim['last_name'] ?>"
               value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
      </div>
      <div class="form__row">
        <label class="form__label" for="user-birthday">Дата народження</label>
        <!-- min/max — щоб у поле не вписався рік на кшталт 222222: без них
             нативний пікер приймає будь-яку кількість цифр у році. Нижня
             межа — 120 років, верхня — вчора (сьогоднішніх новонароджених
             у студії не буває). -->
        <input class="form__input" type="date" id="user-birthday" name="birthday"
               min="<?= date('Y-m-d', strtotime('-120 years')) ?>"
               max="<?= date('Y-m-d', strtotime('-1 day')) ?>"
               value="<?= htmlspecialchars($user['birthday'] ?? '') ?>">
      </div>
      <div class="form__row">
        <label class="form__label" for="user-gender">Стать</label>
        <select class="form__input" id="user-gender" name="gender" data-slimselect>
          <?php foreach (account_genders() as $val => $label): ?>
            <option value="<?= $val ?>"<?= (int)($user['gender'] ?? 0) === $val ? ' selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="account-form__actions">
      <button type="submit" class="btn btn--filled">Зберегти</button>
    </div>
  </form>

  <h3 class="account-content__subtitle">Пошта й телефон</h3>
  <p class="text--sm text--muted">
    Змінюються окремо — новий контакт треба підтвердити.
  </p>

  <!-- Окремо від форми вище — новий контакт треба підтвердити:
       POST /user/phone_update/ {phone} → SMS, далі phone_update_verify/ {code};
       POST /user/email_update/ {email, next_url} — підтвердження листом
       (email тут довідковий, не спосіб входу — див. auth.php). -->
  <dl class="rows account-facts mt-4">
    <div class="rows__item account-contact__item">
      <dt class="label text--muted">Пошта</dt>
      <dd><?= htmlspecialchars($profile['email'] ?: '—') ?></dd>
      <dd class="account-contact__action">
        <button type="button" class="btn btn--sm btn--outlined" data-modal="contact-change?field=email">Змінити</button>
      </dd>
    </div>
    <div class="rows__item account-contact__item">
      <dt class="label text--muted">Телефон</dt>
      <dd><?= htmlspecialchars($profile['phone'] ?: '—') ?></dd>
      <dd class="account-contact__action">
        <button type="button" class="btn btn--sm btn--outlined" data-modal="contact-change?field=phone">Змінити</button>
      </dd>
    </div>
  </dl>

<?php endif; ?>

<?php include 'partials/account/layout-close.php'; ?>

<?php include 'partials/footer.php'; ?>
