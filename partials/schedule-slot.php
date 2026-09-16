<?php
/**
 * Один рядок розкладу. Включається зсередини partials/schedule.php у циклі
 * й читає $sch_slot із нього — власних параметрів не має.
 *
 * Окремий файл, бо та сама розмітка потрібна у трьох місцях (день, тиждень
 * і мобільний варіант місяця), а копія в кожному зробила б зміну поля
 * правкою в трьох місцях.
 *
 * Розмітка — наявний .rows__item--slot: час/назва/локація вже розкладені
 * в грід і вже згортаються в дві колонки під 768px. День винесений у
 * заголовок групи; час і тривалість — одна колонка (.schedule__slot-time),
 * тривалість під годинами, а перед діями — тренер.
 */
// "01:00:00" → "60 хв": секунди в підписі заняття не несуть змісту
[$slot_h, $slot_m] = array_pad(explode(':', (string)$sch_slot['duration']), 2, 0);
$slot_min = (int)$slot_h * 60 + (int)$slot_m;
// seats === 0 — заняття існує, місць нема. seats === null — API його не
// прислало (нема в template), тоді лічильник просто не показуємо.
// blocked — ширше за no_seats: включає й enroll_deadline/минулий час
// (api/schedule.php denormalize()), тому дізейблить «Записатись» навіть
// коли місця формально ще є.
$no_seats = $sch_slot['seats'] === 0;
$blocked  = !$sch_slot['is_bookable'];
$block_label = $no_seats ? 'Немає місць' : 'Недоступно';
?>
<li class="rows__item rows__item--slot schedule__slot<?= $blocked ? ' schedule__slot--blocked' : '' ?>">
  <span class="schedule__slot-time">
    <span class="label rows__num rows__day"><?= $sch_slot['time'] ?></span>
    <span class="text--sm text--muted"><?= $slot_min ? $slot_min . ' хв' : '' ?></span>
  </span>

  <span class="rows__title">
    <?= htmlspecialchars($sch_slot['title']) ?>
    <span class="schedule__meta text--sm text--muted">
      <?php if ($no_seats): ?>
        Немає вільних місць
      <?php elseif ($sch_slot['seats']): ?>
        <?php /* API не має поля вільних місць окремо від загальних — лише
                 seats. Тому «12 місць», а не «3 лишилось»: чесний лічильник
                 потребує другого запиту з availability=available і різниці
                 множин. seats === 0 — виняток, його показуємо як «немає
                 місць» вище, бо це і є пряма відповідь API. */ ?>
        <?= $sch_slot['seats'] ?> місць
      <?php endif; ?>
    </span>
  </span>

  <?php /* Комірка виводиться завжди, навіть порожня: грід розкладає дітей
           по порядку, і без неї дії заняття без тренера з'їхали б у чужу
           колонку.
           TODO (WP): аватар — плейсхолдер-іконка, адреса профілю захардкожена.
           InstaSport віддає
           лише id+name, а локальний $team (index/team/training-*) мапиться
           по імені неоднозначно — двоє тренерів звуться «Вікторія». На WP
           тренер приходить з CMS зі своїм slug і мініатюрою. */ ?>
  <span class="schedule__trainer-wrap">
    <span class="schedule__trainer">
      <?php if ($sch_slot['trainers']): ?>
        <a class="schedule__trainer-link" href="trainer-single.php?trainer=halyna">
          <?php /* Мініатюра захардкожена разом із посиланням (див. TODO вище):
                   на WP і кадр, і slug прийдуть з CMS. Якщо фото немає —
                   лишити всередині іконку замість <img>, рамка під неї
                   вмикається сама (.schedule__trainer-ava:not(:has(img))):
                   <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-user"></use></svg> */ ?>
          <span class="schedule__trainer-ava">
            <!-- alt порожній: імʼя тренера вже є текстом цього ж посилання -->
            <img src="assets/img/team/halyna.jpg" alt="" loading="lazy">
          </span>
          <?= htmlspecialchars(implode(', ', array_column($sch_slot['trainers'], 'name'))) ?>
        </a>
      <?php endif; ?>
    </span>

    <?php if ($sch_slot['location']): ?>
      <span class="text--sm text--muted rows__place">
        <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
        <?= htmlspecialchars($sch_slot['location']) ?>
      </span>
    <?php endif; ?>
  </span>

  <span class="schedule__slot-actions">
    <button type="button" class="btn btn--sm btn--outlined schedule__slot-btn"
            data-modal="slot-details?event=<?= $sch_slot['id'] ?>">Деталі</button>
    <?php if ($blocked): ?>
      <button type="button" class="btn btn--sm btn--filled schedule__slot-btn" disabled><?= $block_label ?></button>
    <?php else: ?>
      <button type="button" class="btn btn--sm btn--filled schedule__slot-btn" data-modal="booking?event=<?= $sch_slot['id'] ?>">Записатись</button>
    <?php endif; ?>
  </span>
</li>
<?php
unset($slot_min);
