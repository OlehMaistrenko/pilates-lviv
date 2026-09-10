<?php
/**
 * Один рядок розкладу. Включається зсередини partials/schedule.php у циклі
 * й читає $sch_slot із нього — власних параметрів не має.
 *
 * Окремий файл, бо та сама розмітка потрібна у трьох місцях (день, тиждень
 * і мобільний варіант місяця), а копія в кожному зробила б зміну поля
 * правкою в трьох місцях.
 *
 * Розмітка — наявний .rows__item--slot: день/час/назва/локація вже
 * розкладені в грід і вже згортаються в дві колонки під 768px.
 * Тут замість дня тижня стоїть час (день винесений у заголовок групи),
 * а другою колонкою йде тривалість.
 */
$slot_variant = ['1' => 'група', '2' => 'персонально', '3' => 'спліт'][(string)($sch_slot['variant'] ?? '')] ?? '';
// "01:00:00" → "60 хв": секунди в підписі заняття не несуть змісту
[$slot_h, $slot_m] = array_pad(explode(':', (string)$sch_slot['duration']), 2, 0);
$slot_min = (int)$slot_h * 60 + (int)$slot_m;
?>
<li class="rows__item rows__item--slot schedule__slot">
  <span class="label rows__num rows__day"><?= $sch_slot['time'] ?></span>
  <span class="text--sm text--muted"><?= $slot_min ? $slot_min . ' хв' : '' ?></span>

  <span class="rows__title">
    <?= htmlspecialchars($sch_slot['title']) ?>
    <?php if ($slot_variant): ?>
      <span class="text--sm text--muted schedule__variant"><?= $slot_variant ?></span>
    <?php endif; ?>
    <span class="schedule__meta text--sm text--muted">
      <?php if ($sch_slot['trainers']): ?>
        <?= htmlspecialchars(implode(', ', array_column($sch_slot['trainers'], 'name'))) ?>
      <?php endif; ?>
      <?php if ($sch_slot['seats']): ?>
        <?php /* API не має поля вільних місць — лише всього. Тому «12 місць»,
                 а не «3 лишилось»: чесний лічильник потребує другого запиту
                 з availability=available і різниці множин. */ ?>
        · <?= $sch_slot['seats'] ?> місць
      <?php endif; ?>
    </span>
  </span>

  <span class="schedule__slot-actions">
    <?php if ($sch_slot['location']): ?>
      <span class="text--sm text--muted rows__place">
        <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-pin"></use></svg>
        <?= htmlspecialchars($sch_slot['location']) ?>
      </span>
    <?php endif; ?>
    <button type="button" class="btn btn--sm btn--filled" data-modal="booking?event=<?= $sch_slot['id'] ?>">Записатись</button>
  </span>
</li>
<?php
unset($slot_variant, $slot_min);
