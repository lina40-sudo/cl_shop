<?php
require 'config.php';
require_once __DIR__ . '/database.php';

$payload    = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$secret     = 'whsec_21667a483d4b88ff9d91cce359d07e5db9e46ea425aa4bbc96983491c0755f0e';

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $secret);
} catch (\Exception $e) {
    http_response_code(400);
    exit();
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $meta    = $session->metadata;

    // Get user_id if logged in
    $user_id = null;
    $stmt = mysqli_prepare($conn, "SELECT user_id FROM user_email WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $meta->email);
    mysqli_stmt_execute($stmt);
    $result  = mysqli_stmt_get_result($stmt);
    $row     = mysqli_fetch_assoc($result);
    $user_id = $row['user_id'] ?? null;

    $product_id = (int)$meta->product_id;
    $quantity   = (int)$meta->quantity;

    // Final stock check
    $check = mysqli_prepare($conn, "SELECT quantity FROM products WHERE product_id = ?");
    mysqli_stmt_bind_param($check, "i", $product_id);
    mysqli_stmt_execute($check);
    $stock     = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    $available = (int)($stock['quantity'] ?? 0);

    // Cap quantity to available stock (don't go negative)
    $final_qty = min($quantity, $available);

    // Prevent duplicate orders (refresh protection)
    $dup = mysqli_prepare($conn, "SELECT order_id FROM orders WHERE email=? AND product_id=? AND created_at > NOW() - INTERVAL 5 MINUTE");
    mysqli_stmt_bind_param($dup, "si", $meta->email, $product_id);
    mysqli_stmt_execute($dup);
    mysqli_stmt_store_result($dup);
    if (mysqli_stmt_num_rows($dup) > 0) {
        http_response_code(200);
        exit;
    }

    // Insert order (trigger will deduct stock automatically)
    $stmt = mysqli_prepare($conn, "INSERT INTO orders 
        (user_id, email, wilaya, last_name, first_name, addresss, 
         apartment, city, phone, product_id, quantity) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssssssii",
        $user_id,
        $meta->email,      $meta->wilaya,    $meta->last_name,
        $meta->first_name, $meta->address,   $meta->apartment,
        $meta->city,       $meta->phone,
        $product_id,       $final_qty
    );
    mysqli_stmt_execute($stmt);
    // ⚠️ No manual UPDATE here — the trigger handles stock deduction automatically
}

http_response_code(200);
