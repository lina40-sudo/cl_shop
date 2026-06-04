<?php
session_start();
include("database.php");

$user_id = null;
if (!empty($_SESSION['email'])) {
    $stmt = mysqli_prepare($conn, "SELECT user_id FROM user_email WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['user_id'] ?? null;
}

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


error_log("DEBUG: product_id=$product_id, quantity=$quantity");
file_put_contents('debug.txt', "product_id=$product_id, quantity=$quantity\n", FILE_APPEND);

//CHECK STOCK BEFORE INSERTING
$check = mysqli_prepare($conn, "SELECT quantity FROM products WHERE product_id = ? AND quantity >= ?");
mysqli_stmt_bind_param($check, "ii", $product_id, $quantity);
mysqli_stmt_execute($check);
$check_result = mysqli_stmt_get_result($check);
if (mysqli_num_rows($check_result) === 0) {
    echo json_encode(['success' => false, 'error' => 'Not enough stock available.']);
    exit;
}

// Insert into database
$stmt = mysqli_prepare($conn, "INSERT INTO orders 
    (user_id, email, wilaya, last_name, first_name, addresss, apartment, city, phone, product_id, quantity) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, "issssssssii",
    $user_id, $email, $wilaya, $last_name, $first_name,
    $address, $apartment, $city, $phone, $product_id, $quantity
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['order_id'] = mysqli_insert_id($conn);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}