<?php
/**
 * Події — список карток-рядків: дата, назва, короткий опис, «Детальніше»
 * (веде на event-single.php) і кнопка запису.
 * Один блок на дві сторінки: events.php і потоки навчального центру.
 *
 * Перед include задай:
 *   $events_items — [слаг => подія, …] у формі partials/events-data.php.
 *                   Обовʼязково. Потрібні поля: date, title; решта опційні
 *                   (when, note).
 *   $events_title — заголовок секції; '' — секція без шапки.
 *   $events_link  — ['текст', 'href'] праворуч у шапці. Опційно.
 *   $events_class — додатковий клас на <section> (напр. 'pt-0'). Опційно.
 *   $events_empty — текст, якщо список порожній (фільтр нічого не знайшов).
 *
 * Картка не суцільне посилання, а два окремі виходи (деталі й запис) —
 * як у референсі: більшість тих, хто вже знає подію, тисне одразу
 * «Записатись», і вести їх спершу на детальну означало б зайвий крок.
 * Тому й hover тут тільки на самих кнопках, не на рядку (CLAUDE.md).
 *
 * Змінні скидаються в кінці.
 */
$events_items = $events_items ?? [];
$events_title = $events_title ?? 'Найближчі події';
$events_link  = $events_link  ?? null;
$events_class = $events_class ?? '';
$events_empty = $events_empty ?? 'Тут поки порожньо. Подзвоніть — скажемо, коли буде наступна.';
?>
<section class="section<?= $events_class ? ' ' . $events_class : '' ?>">
  <div class="container container--narrow">
    <?php if ($events_title): ?>
      <div class="section-head<?= $events_link ? ' section-head--split' : '' ?>">
        <h2 data-reveal="lines"><?= $events_title ?></h2>
        <?php if ($events_link): ?>
          <a class="btn btn--outlined" href="<?= $events_link[1] ?>"><?= $events_link[0] ?></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!$events_items): ?>
      <p class="text--lead text--muted"><?= $events_empty ?></p>
    <?php else: ?>
      <div class="events" data-reveal>
        <?php foreach ($events_items as $slug => $e): ?>
          <article class="event">
            <div class="event__when">
              <p class="event__date">
                <?php if (!empty($e['dt'])): ?>
                  <time datetime="<?= $e['dt'] ?>"><?= $e['date'] ?></time>
                <?php else: ?>
                  <?= $e['date'] ?>
                <?php endif; ?>
              </p>
              <?php if (!empty($e['when'])): ?>
                <p class="text--sm text--muted mt-1"><?= $e['when'] ?></p>
              <?php endif; ?>
            </div>

            <div>
              <h3 class="event__title">
                <a href="event-single.php?event=<?= $slug ?>"><?= $e['title'] ?></a>
              </h3>
              <?php if (!empty($e['note'])): ?>
                <p class="text--muted event__note"><?= $e['note'] ?></p>
              <?php endif; ?>

              <div class="event__actions">
                <?php /* aria-label, а не просто «Записатись»: у списку таких
                         кнопок шість поспіль, і без назви події вони
                         нерозрізненні для скрінрідера */ ?>
                <button type="button" class="btn btn--filled btn--sm"
                        data-modal="callback?event=<?= rawurlencode($e['title']) ?>"
                        aria-label="Записатись: <?= htmlspecialchars($e['title']) ?>">Записатись</button>
                <a class="btn btn--outlined btn--sm" href="event-single.php?event=<?= $slug ?>">Детальніше</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php
unset($events_items, $events_title, $events_link, $events_class, $events_empty);
