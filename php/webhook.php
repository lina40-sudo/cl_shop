<?php
require 'config.php';
require_once __DIR__ . '/database.php';

$payload    = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$secret     = 'whsec_21667a483d4b88ff9d91cce359d07e5db9e46ea425aa4bbc96983491c0755f0e'; // replace this later

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

    // Save order to database
    $stmt = mysqli_prepare($conn, "INSERT INTO orders 
        (user_id, email, wilaya, last_name, first_name, addresss, 
         apartment, city, phone, product_id, quantity) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "issssssssii",
        $user_id,
        $meta->email,     $meta->wilaya,    $meta->last_name,
        $meta->first_name, $meta->address,  $meta->apartment,
        $meta->city,      $meta->phone,
        $product_id,      $quantity
    );
    mysqli_stmt_execute($stmt);

    // Decrease stock
    $upd = mysqli_prepare($conn,
        "UPDATE products SET quantity = quantity - ? WHERE product_id = ?");
    mysqli_stmt_bind_param($upd, "ii", $quantity, $product_id);
    mysqli_stmt_execute($upd);
}

http_response_code(200);