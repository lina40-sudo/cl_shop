<?php
require 'config.php';
require_once __DIR__ . '/cart-lib.php';

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost/CLwebsite';

$payload = cart_as_payload();
$items = $payload['items'] ?? [];

if (!is_array($items) || count($items) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

// Get form data sent from checkout.php
$email      = trim($_POST['email']      ?? '');
$wilaya     = trim($_POST['wilaya']     ?? '');
$last_name  = trim($_POST['last-name']  ?? '');
$first_name = trim($_POST['first-name'] ?? '');
$address    = trim($_POST['address']    ?? '');
$apartment  = trim($_POST['apartment']  ?? '');
$city       = trim($_POST['city']       ?? '');
$phone      = trim($_POST['phone']      ?? '');
$product_id = intval($_POST['product_id'] ?? 1);
$quantity   = intval($_POST['quantity']   ?? 1);

$lineItems = [];
foreach ($items as $it) {
    $name = (string)($it['name'] ?? 'Item');
    $price = (int)($it['price'] ?? 0);
    $qty = (int)($it['qty'] ?? 1);
    $img = (string)($it['image'] ?? '');
    if ($price <= 0 || $qty <= 0) continue;

    $productData = ['name' => $name];
    if ($img !== '') {
        if (preg_match('/^https?:\/\//i', $img)) {
            $productData['images'] = [$img];
        }
    }

    $lineItems[] = [
        'price_data' => [
            'currency' => 'usd',
            'product_data' => $productData,
            'unit_amount' => $price,
        ],
        'quantity' => $qty,
    ];
}

if (count($lineItems) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart items invalid']);
    exit;
}


// ── STOCK CHECK ──────────────────────────────────────────
include("database.php");

$check = mysqli_prepare($conn, "SELECT quantity, is_in_stock FROM products WHERE product_id = ?");
mysqli_stmt_bind_param($check, "i", $product_id);
mysqli_stmt_execute($check);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

if (!$row || $row['is_in_stock'] == 0 || $row['quantity'] < $quantity) {
    http_response_code(400);
    echo json_encode(['error' => 'out_of_stock']);
    exit;
}
// ─────────────────────────────────────────────────────────


$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $lineItems,
    'mode' => 'payment',
    'customer_email' => $email,
    'metadata' => [
        'email'      => $email,
        'wilaya'     => $wilaya,
        'last_name'  => $last_name,
        'first_name' => $first_name,
        'address'    => $address,
        'apartment'  => $apartment,
        'city'       => $city,
        'phone'      => $phone,
        'product_id' => $product_id,
        'quantity'   => $quantity,
    ],
    'success_url' => $YOUR_DOMAIN . '/success.php',
    'cancel_url'  => $YOUR_DOMAIN . '/cancel.php',
]);

echo json_encode(['id' => $session->id]);
