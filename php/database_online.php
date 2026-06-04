<?php
$db_server = "sql309.infinityfree.com";
$db_username = "if0_42026869";
$db_password = "cPWR0y22jzVA";
$db_name = "if0_42026869_accounts";
$port = 3306;

$conn = mysqli_connect($db_server, $db_username, $db_password, $db_name, $port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
