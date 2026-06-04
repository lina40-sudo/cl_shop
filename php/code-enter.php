<?php
session_start();
ob_start();
include("database.php");
$error = "";
$success = "";
// var_dump($_SESSION);
// var_dump($_SESSION);
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $code_entered = trim($_POST["code"]);

    if (empty($code_entered)) {
        $_SESSION['error'] = "Please enter the code";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    elseif ($code_entered != $_SESSION['verification_code']){
        $_SESSION['error'] = "Enter the correct 6-digit code";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
   else if ($code_entered == $_SESSION['verification_code']) {

    $check = $conn->prepare("SELECT user_id FROM user_email WHERE email = ?");
    $check->bind_param("s", $_SESSION['pending_email']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO user_email (email) VALUES (?)");
        $stmt->bind_param("s", $_SESSION['pending_email']);
        $stmt->execute();
    }

    $_SESSION['email'] = $_SESSION['pending_email']; // now verified
    unset($_SESSION['verification_code']);
    unset($_SESSION['pending_email']);
    session_write_close();
    header("Location: user-profile.php");
    exit();
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="account.css">
    <title>document</title>
</head>
<body>


<h4>Enter Code</h4>
<p>sent to <?php echo isset($_SESSION['pending_email']) ? $_SESSION['pending_email'] : ''; ?></p>



<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <input type="text" style="<?php if (!empty($_SESSION['error'])) echo 'border:2px solid red;'; ?>" name="code" class="input-1"  placeholder="Enter 6-digit code">
    <?php if (!empty($_SESSION['error'])) : ?>
        <p style='color:red'; margin:5px 0; font-size:14px;>
            <?php echo  $_SESSION['error'];
        unset($_SESSION['error']); 

         ?>
        </p>
    <?php endif; ?>

    <input type="submit" class="input-2" name="Verify" value="Verify">

</form>
<a href="email-enter.php">sign in with a different email</a>
</body>
</html>

<?php
$content = ob_get_clean();
include("account.php");
?>