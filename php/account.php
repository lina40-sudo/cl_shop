<?php

use Stripe\Terminal\Location;

if (basename($_SERVER['PHP_SELF']) == 'account.php') {
    http_response_code(403);
    exit("Access denied");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- <title>Document</title> -->
    <title>Sign in - CLshop.com</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="header.css"> -->

    <link rel="stylesheet" href="account.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='0' fill='%232A23BE'/><text y='72' font-size='60' font-family='Arial' font-weight='400' fill='white' text-anchor='middle' x='50'>CL</text></svg>">
</head>
<body>

<div class="page-account">
    <div class="account-container">

        <div class="logo">
       <a href="index.php">
        <div class="left-header-1">
            <div class="logo-inner">
                <h1>CL</h1>
            </div>
            <div class="labo-name">
                <p>Powered by <br> Perfect Skin</p>
            </div>
        </div>
       </a>
        </div>

        
        <?php
      if (isset($content)) {
      echo $content;
}
?>

    </div>
</div>

</body>
</html>

