<!-- ============================================================
     05 · Переваги — фулскрін-пін: заголовок, кадр, текст. Кадри
     міняються шторкою (clip-path, той самий прийом, що в блоках
     одометра), тексти — стрибком на середині кроку, зсувом рядків зі
     стагером. Розрахована на JS (js/main.js, data-anim="pinstack");
     без нього видно перший аргумент статично.
     ============================================================ -->
<section class="pinstack" data-anim="pinstack">
  <!-- Панелі — h3 під цим h2: аргументи однорідні, тож рівень один -->
  <h2 class="sr-only">Чому саме тут</h2>
  <div class="pinstack__stage">
    <!-- Фон: той самий кадр, що й у картці, але на весь екран. Кожен
         наступний відкривається шторкою знизу вгору (clip-path, як у
         блоках одометра) — тягнеться зі скролом. -->
    <div class="pinstack__bgs" aria-hidden="true">
      <?php foreach ($advantages as $a): ?>
        <div class="pinstack__bg" style="background-image: url('assets/img/<?= $a[2] ?>')"></div>
      <?php endforeach; ?>
    </div>

    <!-- Картка: заголовок → кадр → текст. Кадри тут окремим стосом, а не
         в панелі: текст міняється стрибком на середині кроку, а кадр
         тягнеться шторкою зі скролом — різні ритми, тож і різні шари.
         Стос стоїть у 2-му рядку гріда, між заголовком і текстом. -->
    <div class="pinstack__card">
      <div class="pinstack__shots" aria-hidden="true">
        <?php foreach ($advantages as $a): ?>
          <img class="pinstack__shot" src="assets/img/<?= $a[2] ?>" alt=""
               width="1600" height="1067" loading="lazy">
        <?php endforeach; ?>
      </div>

      <?php foreach ($advantages as $i => $a): ?>
        <article class="pinstack__panel<?= $i ? '' : ' is-active' ?>">
          <h3 class="pinstack__title"><?= $a[0] ?></h3>
          <p class="pinstack__desc"><?= $a[1] ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
