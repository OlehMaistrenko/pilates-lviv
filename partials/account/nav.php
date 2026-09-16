<?php
/**
 * Бокова навігація кабінету. Перед include задай $account_nav — ключ
 * активного розділу (той самий принцип, що $nav у header.php).
 */
$account_nav = $account_nav ?? '';

// Пункту «Зміна паролю» тут навмисно немає: скидання пароля йде через
// SMS-код у модалці входу (partials/modals/auth.php?step=reset), окрема
// сторінка кабінету для цього не потрібна.
$account_menu = [
  'profile'  => ['Персональна інформація', 'account.php',          'icon-user'],
  'cards'    => ['Абонементи',             'account-cards.php',    'icon-card'],
  'visits'   => ['Мої заняття',            'account-visits.php',   'icon-calendar'],
  'deposits' => ['Рахунок і поповнення',   'account-deposits.php', 'icon-wallet'],
];
?>
<nav class="account-nav" aria-label="Розділи кабінету">
  <ul class="account-nav__list">
    <?php foreach ($account_menu as $key => [$label, $href, $icon]): ?>
      <?php $is_current = $key === $account_nav; ?>
      <li>
        <a class="account-nav__link<?= $is_current ? ' is-current' : '' ?>"
           href="<?= $href ?>"<?= $is_current ? ' aria-current="page"' : '' ?>>
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#<?= $icon ?>"></use></svg>
          <span><?= $label ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <form class="account-nav__logout" action="" method="post" data-remote>
    <button type="submit" class="account-nav__link account-nav__link--logout">
      <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-logout"></use></svg>
      <span>Вийти</span>
    </button>
  </form>
</nav>
