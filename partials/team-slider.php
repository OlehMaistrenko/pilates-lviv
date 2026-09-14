<?php
/**
 * Тренери — слайдер портретів-арок. Перед include задай:
 *   $team_items — [[імʼя, напрямок, цитата, slug], …]. Обовʼязково.
 *                 (Форма з головної; team.php має свою сітку .team-grid, не цей блок.)
 *   $team_title — заголовок секції.
 *   $team_lead  — лід-абзац праворуч у шапці. Опційно.
 *   $team_all   — показувати картку-посилання «Уся команда» в кінці. Дефолт true.
 *   $team_class — додатковий клас на <section> (напр. 'pt-0'). Опційно.
 *
 * Потребує $vendor_swiper = true на сторінці (до include footer.php).
 * Змінні скидаються в кінці.
 */
$team_items = $team_items ?? [];
$team_title = $team_title ?? 'Тренери';
$team_lead  = $team_lead  ?? null;
$team_all   = $team_all   ?? true;
$team_class = $team_class ?? '';

if ($team_items):
?>
<section class="section team<?= $team_class ? ' ' . $team_class : '' ?>">
  <div class="container">
    <div class="section-head section-head--split">
      <h2 data-reveal="lines"><?= $team_title ?></h2>
      <?php if ($team_lead): ?>
        <p class="text--lead text--muted" data-reveal><?= $team_lead ?></p>
      <?php endif; ?>
    </div>

    <div class="swiper-wrap" data-reveal>
      <div class="swiper" data-swiper='{"slidesPerView":1.2,"spaceBetween":24,"breakpoints":{"769":{"slidesPerView":2.2},"1081":{"slidesPerView":4},"1440":{"slidesPerView":5}}}'>
        <ul class="swiper-wrapper">
          <?php foreach ($team_items as [$name, $role, $quote, $slug]): ?>
            <li class="trainer swiper-slide">
              <a class="trainer__link" href="trainer-single.php?trainer=<?= $slug ?>">
                <span class="trainer__media media-swap">
                  <img class="media-swap__img" src="assets/img/team/<?= $slug ?>.jpg" alt="<?= htmlspecialchars($name) ?>, <?= mb_strtolower($role) ?>" loading="lazy">
                  <img class="media-swap__img media-swap__hover" src="assets/img/team/<?= $slug ?>-hover.jpg" alt="" loading="lazy">
                </span>
                <span class="trainer__name"><?= $name ?></span>
                <span class="text--sm text--muted"><?= $role ?></span>
                <q class="card__note text--muted"><?= $quote ?></q>
              </a>
            </li>
          <?php endforeach; ?>
          <?php if ($team_all): ?>
            <li class="trainer trainer--all swiper-slide">
              <a class="trainer__link" href="team.php">
                <span class="trainer__media">
                  <svg class="trainer__all-mark" viewBox="235 127 135 139" aria-hidden="true"><path d="M341.2,147.2c-5.3-1.7-15.6-2.5-22.3,0v-11.9s6.1,4.8,6.1,4.8l4.9-4.8,4.9,4.8,6.4-4.8v11.9Z"/><path d="M261.6,220.1l-15.8-29.5s4.3-1,4.1-1.6c-.7-2.4-5.4-.8-5.4-.8l-1.7-3.2c1-.2,8.7-2.8,12.7,2.2,2.8,3.5,9.9,17.7,10.3,18.1,4.6,1.4,21.7,5.7,25.8,7.4s11.8,15.4,11.8,15.4c2.5-4.1,11.4-19.7,4.7-23.1-3.5-1.7-29.9-8.1-34.7-9.4l-3.7-7.5s4-1.1,3.8-1.6c-.7-2.4-5.1-.9-5.1-.9l-1.3-2.4c2.6-.8,9.1-1.4,11.4,1.5,1.1,1.4,2.1,3,2.1,3,0,0,29.9.3,33.5.2,3.6,0,6.8,0,9.9-1.4s8.4-10.2,4.5-18.9c5.6-3,0-10.2-5.4-4.1,0,0,3.2,4.8,3.7,9.7s.2,7-3.7,9.8c0,0-1.1-2.8-2-2.9-2.9-.3-10.5,10.5-11.5.3,0,0,4.3.9,5.2-.3,2.7-3.8-1.8-4.2-6.9-1.8l-4.3-6.1,7.6-6.6-.4-1.1c-.3-.7,0-1.5.6-1.8l5.9-4.2-3-5.6c10.9-5.8,30.3-4.3,39.3,3.9,15.1,13.6,8,35.8-4.4,41.4,0,0-21.6,40.7-27.8,52.4s4,1.7,4.7,1.7c8.1,0,9.7-9.2,11.5-20.2,2.2-13.8,8.1-18.7,13.4-19,3-.1,7.5,0,9.1,0s.5.5.4.8c-.7,1.3-3.8,7.2-5.2,9.9-1.1,2.1-2.7,2.3-5,2.3-5.7,0-7.3,2.1-8.7,10.4-1.6,11.6-4.8,21.6-18.7,21.6h-30.9s-13.7-25.5-17-31.4c-2.9-4.3-8.2-6.6-13.3-6.5ZM319.1,165.7s-3.2-.5-5,1.3-2.1,3.3-2,3.4c0,0,4-.4,4.4-.7,1.9-2,2.7-4,2.7-4Z"/></svg>
                  <span class="trainer__all">Уся команда</span>
                </span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="slider-controls mt-5">
        <button type="button" class="btn btn--icon btn--outlined swiper-prev" aria-label="Попередній тренер">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
        <div class="swiper-pagination"></div>
        <button type="button" class="btn btn--icon btn--outlined swiper-next" aria-label="Наступний тренер">
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-arrow-right"></use></svg>
        </button>
      </div>
    </div>
  </div>
</section>
<?php endif;

unset($team_items, $team_title, $team_lead, $team_all, $team_class);
