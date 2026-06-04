<?php
session_start();
ob_start();
include("database.php");

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);

    // 1. Check empty
   if (empty($email)) {
    $_SESSION['error'] = "Email is required";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
    else {

        // 🔹 Generate 6-digit code
        $code = rand(100000, 999999);

        // 🔹 Store in session
        $_SESSION['verification_code'] = $code;
        $_SESSION['pending_email'] = $email;

       

      
    

        // 🔹 Create mail object
        $mail = new PHPMailer(true);

        try {
            // 🔹 SMTP settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'cl.shop1328@gmail.com'; // 🔁 CHANGE
            $mail->Password = 'kxbz lbmy yyus pgah';    // 🔁 CHANGE
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // 🔹 Email setup
            $mail->setFrom('cl.shop1328@gmail.com', 'CL Shop');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verification Code';
            $mail->Body = "Your verification code is: <b>$code</b>";

            // 🔹 Send email
            $mail->send();

            // redirect to code page
           header("Location: code-enter.php");
           exit();

            
            
        } catch (Exception $e) {
            $error = "❌ Error: " . $mail->ErrorInfo;
        }
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
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css"> -->

    <title>Sign in - CLshop.com</title>
</head>
<body>
    
           <h4>Sign in</h4>
           <p>Sign in or create an account</p>
              <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
<input type="email" style="<?php if (!empty($_SESSION['error'])) echo 'border:2px solid red;'; ?>" class="input-1" name="email" placeholder="Email">
                 <!-- ERROR MESSAGE HERE -->

<?php if (!empty($_SESSION['error'])) : ?>
    <p style="color:red; margin:5px 0; font-size:14px;">
        <?php echo  $_SESSION['error'];
        unset($_SESSION['error']); 
         ?>
    </p>
<?php endif; ?>

                <input type="submit" class="input-2" name="continue" value="Continue">

                </form>
</body>
</html>

<?php
$content = ob_get_clean();
include("account.php");
?>
