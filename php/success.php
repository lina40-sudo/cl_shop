<?php
session_start();
include("database.php");

if (!empty($_SESSION['order'])) {
    $o = $_SESSION['order'];
    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (user_id, email, wilaya, last_name, first_name, addresss, apartment, city, phone)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "issssssss",
        $o['user_id'], $o['email'], $o['wilaya'], $o['last_name'], $o['first_name'],
        $o['address'], $o['apartment'], $o['city'], $o['phone']
    );
    mysqli_stmt_execute($stmt);
    unset($_SESSION['order']);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='0' fill='%232A23BE'/><text y='72' font-size='60' font-family='Arial' font-weight='400' fill='white' text-anchor='middle' x='50'>CL</text></svg>">

</head>
<body>

<h1>✅ Payment Successful!</h1>
<p>Thank you for your purchase.</p>

</body>
</html>
