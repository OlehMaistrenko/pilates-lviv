<!-- ============================================================
     Цінності — фулскрін-пін: заголовок, кадр, текст. Кадри
     міняються шторкою (clip-path, той самий прийом, що в блоках
     одометра), тексти — стрибком на середині кроку, зсувом рядків зі
     стагером. Розрахована на JS (js/main.js, data-anim="pinstack");
     без нього видно перший аргумент статично.
     ============================================================ -->
<section class="pinstack" data-anim="pinstack">
  <div class="pinstack__stage">
    <!-- Заголовок усередині стейджа, а не над ним: секція вища за екран
         (--steps × --step-vh), тож поза стейджем він проїхав би повз ще
         до піну й ліг на голу смугу --clr-ink. Панелі — h3 під цим h2:
         аргументи однорідні, тож рівень один. -->
    <h2 class="pinstack__heading">Що для нас важливо</h2>
    <!-- Фон і кадр у картці — РІЗНІ знімки одного сюжету: $a[2] на весь
         екран, $a[3] у картці. Якщо четвертого немає, картка бере той
         самий кадр, що й фон (стара, трикомпонентна форма $advantages).
         Кожен наступний фон відкривається шторкою знизу вгору
         (clip-path, як у блоках одометра) — тягнеться зі скролом. -->
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
