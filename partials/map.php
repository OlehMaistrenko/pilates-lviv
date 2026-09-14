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
$map_token = (file_exists($__cfg) ? (require $__cfg) : [])['mapbox'] ?? '';
if (strncmp($map_token, 'pk.', 3) !== 0) $map_token = '';
$vendor_map = $map_token !== '';
?>
<section class="section map" id="map">
  <div class="container">
    <div class="map__grid">
      <div class="map__canvas" data-map data-token="<?= htmlspecialchars($map_token) ?>"
           data-active="<?= htmlspecialchars($map_active) ?>" data-lenis-prevent-zoom></div>

      <aside class="map__panel" aria-label="Зали та маршрут">
        <div class="map__tools">
          <button type="button" class="btn btn--sm btn--filled" data-map-locate>
            <svg class="icon icon--sm" aria-hidden="true"><use href="assets/icons/sprite.svg#icon-locate"></use></svg>
            Де я
          </button>
          <div class="filters" role="group" aria-label="Спосіб пересування">
            <button type="button" class="btn btn--sm btn--tab is-current" data-map-mode="walking" aria-pressed="true">Пішки</button>
            <button type="button" class="btn btn--sm btn--tab" data-map-mode="driving" aria-pressed="false">Авто</button>
          </div>
          <p class="text--sm text--muted map__hint" data-map-hint hidden></p>
        </div>

        <ul class="map__list">
          <?php foreach ($contact['locations'] as $l): ?>
            <li class="map__hall<?= $l['slug'] === $map_active ? ' is-current' : '' ?>"
                data-hall data-slug="<?= $l['slug'] ?>" data-lat="<?= $l['lat'] ?>" data-lng="<?= $l['lng'] ?>">
              <button type="button" class="map__hall-name" data-map-pick><?= htmlspecialchars($l['label']) ?></button>
              <p class="text--sm text--muted"><?= htmlspecialchars($l['address']) ?></p>
              <p class="text--sm map__route" data-map-route hidden></p>
              <div class="map__actions">
                <a class="text--sm" href="location-single.php?loc=<?= $l['slug'] ?>">Про зал</a>
                <a class="text--sm" href="schedule.php?location=<?= $l['hall'] ?>">Розклад</a>
                <a class="text--sm" data-map-gmaps target="_blank" rel="noopener"
                   href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= $l['lat'] ?>,<?= $l['lng'] ?>&amp;travelmode=walking">Google Maps</a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </aside>
    </div>
  </div>
</section>
<?php unset($map_active, $map_token, $__cfg); ?>
