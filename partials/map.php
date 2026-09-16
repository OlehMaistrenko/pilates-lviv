<?php
/**
 * Карта локацій — Mapbox GL + маршрут від позиції відвідувача до обраного
 * залу (Mapbox Directions). Перед include (опційно):
 *   $map_active — slug залу, вибраного одразу. Дефолт — ?loc= з URL.
 *
 * Зали — всі з $contact['locations'] (header.php), включно з lat/lng/hall.
 * Даних для JS окремим JSON немає: <li> списку і є джерелом (data-*) —
 * той самий елемент працює статичним фолбеком без JS/токена.
 *
 * Токен — у api/config.local.php (поза git, той самий файл, що InstaSport):
 *   'mapbox' => 'pk.…'
 * У браузер іде ТІЛЬКИ публічний pk.* з обмеженням по URL у Mapbox-дашборді;
 * Directions API з ним теж ходить напряму з браузера — це штатний режим,
 * прокладка через api/ не потрібна. Без токена mapbox-gl.js (1,8 МБ) не
 * вантажимо взагалі: $vendor_map лишається порожнім, сторінка — список.
 */
$map_active = $map_active ?? ($_GET['loc'] ?? '');

$__cfg = __DIR__ . '/../api/config.local.php';
/* Три звичайні стейтменти замість одного виразу: підписка [...] на
   дужковому тернарнику з require всередині збиває PHP-граматику
   редактора, і далі по файлу їдуть напрямні відступів. Поведінка та сама. */
$__conf = file_exists($__cfg) ? require $__cfg : [];
$map_token = $__conf['mapbox'] ?? '';
if (strncmp($map_token, 'pk.', 3) !== 0) $map_token = '';
$vendor_map = $map_token !== '';

/* Екрануємо заздалегідь, а не в самому атрибуті: <?= ?> усередині лапок
   збиває HTML-граматику редактора (лапка відкриває рядок, а ?> закриває
   його не там), і далі по файлу їдуть напрямні відступів та парність
   дужок. На вивід це не впливало — суто читабельність. */
$map_attr_token  = htmlspecialchars($map_token);
$map_attr_active = htmlspecialchars($map_active);

/* Тексти, які map.js пише сам. Живуть у розмітці, а не в скрипті, щоб
   локалізувались разом зі сторінкою (у WP — кожен рядок у __()).
   {…} — плейсхолдери, їх підставляє map.js; порядок слів вільний. */
$map_i18n = [
  'hint'       => 'Натисніть «Де я», щоб побачити маршрут.',
  'locating'   => 'Визначаємо…',
  'noGeo'      => 'Геолокація недоступна — потрібен https.',
  'denied'     => 'Доступ до геолокації заборонено — маршрут не побудуємо.',
  'failed'     => 'Не вдалося визначити позицію, спробуйте ще раз.',
  'm'          => '{n} м',
  'km'         => '{n} км',
  'min'        => '{m} хв',
  'hMin'       => '{h} год {m} хв',
  'straight'   => '~{dist} по прямій',
  /* Без «пішки»/«автом»: режим уже видно по натиснутій кнопці над списком,
     тож у кожному рядку це був повтор. Ключі лишаються по режимах —
     renderHall() бере рядок саме за назвою режиму. */
  'walking'    => '{dist} · {time}',
  'driving'    => '{dist} · {time}',
  'ctrlZoom'   => 'Ctrl + скрол, щоб масштабувати',
  'cmdZoom'    => '⌘ + скрол, щоб масштабувати',
  'twoFingers' => 'Рухайте карту двома пальцями',
];
$map_attr_i18n = htmlspecialchars(json_encode($map_i18n, JSON_UNESCAPED_UNICODE));
?>
<!-- Полотно лежить абсолютом на всю секцію, а контейнер із панеллю йде
     поверх нього звичайним потоком. Висоту секції задає CSS: абсолютний
     елемент її не тримає. -->
<section class="map" id="map">
  <div class="map__canvas" data-map data-token="<?= $map_attr_token ?>" data-active="<?= $map_attr_active ?>" data-i18n="<?= $map_attr_i18n ?>" data-lenis-prevent-zoom></div>

  <div class="container map__over">
    <!-- .on-dark: панель мохова (див. styles.css), тож текст, лінії й
         золото мусять перемкнутись на світлі токени -->
    <aside class="map__panel on-dark" aria-label="Зали та маршрут" data-lenis-prevent>
      <!-- Режим ліворуч, «Де я» праворуч: розводить їх space-between, тож
           окремі групи-обгортки більше не потрібні. -->
      <div class="map__tools">
        <div class="filters" role="group" aria-label="Спосіб пересування">
          <button type="button" class="btn btn--sm btn--tab is-current" data-map-mode="walking" aria-pressed="true">Пішки</button>
          <button type="button" class="btn btn--sm btn--tab" data-map-mode="driving" aria-pressed="false">Авто</button>
        </div>
        <button type="button" class="btn btn--sm  btn--filled" data-map-locate>
          <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-locate"></use></svg>
          Де я
        </button>
        <p class="text--sm text--muted map__hint" data-map-hint hidden></p>
      </div>

      <ul class="map__list">
        <?php foreach ($contact['locations'] as $l): ?>
          <?php /* клас рахуємо до тега: тернарник усередині лапок плюс
                   розрив тега — та сама пара, що збиває підсвітку */ ?>
          <?php $hall_class = 'map__hall' . ($l['slug'] === $map_active ? ' is-current' : ''); ?>
          <li class="<?= $hall_class ?>" data-hall data-slug="<?= $l['slug'] ?>" data-lat="<?= $l['lat'] ?>" data-lng="<?= $l['lng'] ?>">
            <button type="button" class="map__hall-name" data-map-pick><?= htmlspecialchars($l['label']) ?></button>
            <p class="text--sm text--muted"><?= htmlspecialchars($l['address']) ?></p>
            <p class="text--sm map__route" data-map-route hidden></p>
            <a class="btn btn--sm btn--filled btn--block mt-3" href="location-single.php?loc=<?= $l['slug'] ?>">Про зал</a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>
  </div>
</section>
<?php unset($map_active, $map_token, $__cfg, $__conf, $map_attr_token, $map_attr_active, $map_i18n, $map_attr_i18n); ?>
