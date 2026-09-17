<?php
/**
 * AJAX-ендпоінт для модалок. Повертає лише HTML-фрагмент (без header/footer).
 * GET name — ключ фрагмента з whitelist нижче; решта $_GET прозоро долітає
 * до конкретного фрагмента (напр. ?name=info&id=2 → info.php читає $_GET['id']).
 */
$modals = ['example', 'booking', 'callback', 'partnership', 'thanks', 'slot-details', 'auth', 'contact-change', 'promo'];
$name = $_GET['name'] ?? '';

if (!in_array($name, $modals, true)) {
  http_response_code(404);
  exit;
}

header('Content-Type: text/html; charset=utf-8');
include __DIR__ . '/' . $name . '.php';
