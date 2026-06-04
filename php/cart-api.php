<?php
declare(strict_types=1);

require_once __DIR__ . '/cart-lib.php';

header('Content-Type: application/json; charset=utf-8');

function json_out(int $status, array $payload): void {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

$action = (string)($_GET['action'] ?? $_POST['action'] ?? 'get');

if ($action === 'get') {
    json_out(200, ['ok' => true, 'cart' => cart_as_payload()]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(405, ['ok' => false, 'error' => 'Method not allowed']);
}

$raw = file_get_contents('php://input');
$json = null;
if (is_string($raw) && trim($raw) !== '') {
    $json = json_decode($raw, true);
}
if (!is_array($json)) $json = [];

// Accept either JSON body or normal form fields
$id = (string)($json['id'] ?? $_POST['id'] ?? '');
$name = (string)($json['name'] ?? $_POST['name'] ?? '');
$image = (string)($json['image'] ?? $_POST['image'] ?? '');
$price = (int)($json['price'] ?? $_POST['price'] ?? 0); // cents
$qty = (int)($json['qty'] ?? $_POST['qty'] ?? 1);

switch ($action) {
    case 'add':
        if ($id === '' || $name === '' || $price <= 0) {
            json_out(400, ['ok' => false, 'error' => 'Missing product data']);
        }
        cart_add($id, $name, $price, $image, $qty);
        json_out(200, ['ok' => true, 'cart' => cart_as_payload()]);

    case 'setQty':
        if ($id === '') json_out(400, ['ok' => false, 'error' => 'Missing id']);
        cart_update_qty($id, $qty);
        json_out(200, ['ok' => true, 'cart' => cart_as_payload()]);

    case 'remove':
        if ($id === '') json_out(400, ['ok' => false, 'error' => 'Missing id']);
        cart_remove($id);
        json_out(200, ['ok' => true, 'cart' => cart_as_payload()]);

    case 'clear':
        cart_clear();
        json_out(200, ['ok' => true, 'cart' => cart_as_payload()]);

    default:
        json_out(400, ['ok' => false, 'error' => 'Unknown action']);
}

