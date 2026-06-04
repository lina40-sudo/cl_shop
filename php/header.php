<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/cart-lib.php';
$cartTotals = cart_totals();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="header.css">
  <?php if (empty($HEADER_DISABLE_BOOTSTRAP)): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <?php endif; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Document</title>
</head>
<body>
  

<div class="header-page1">
    <!-- left side of header -->
    <div class="left-header-1" onclick="window.location.href='index.php'">
     
     <div class="logo">
      <h1>CL</h1>
     </div>
     <div class="labo-name">
      <p>
        Powered by <br> Perfect Skin
      </p> 

</div>
    </div>
<!-- right side of header -->
<div class="nav">
  <div class="navbar-nav">
          <a class="nav-link" href="shop.php">
            SHOP
          </a>
    
   </div>
    <!--right icons -->
    <div class="nav-icons">
     
         <!-- <div class="nav-icons-left"> <a href="email-enter.php"><i class="fas fa-user"></i></a></div> -->
    <div class="nav-icons-left">
    <?php
    if (isset($_SESSION['email'])) {
    
    // echo '<a href="user-profile.php"><i class="fas fa-user"></i></a>';
     echo '
    <div class="user-menu">
        <a href="user-profile.php"><i class="fas fa-user"></i></a>

        <div class="logout-box">
            <a href="logout.php">Logout</a>
        </div>
    </div>
    ';


} else {
    
    echo '<a href="email-enter.php"><i class="fas fa-user"></i></a>';
}
      ?>
</div>
      <div class="nav-icons-right">

        <a href="#" onclick="openBag(event)">
  <i class="fas fa-shopping-bag"></i>
  <span class="bag-count" id="bagCount"><?php echo (int)($cartTotals['count'] ?? 0); ?></span>
</a>
      </div>

      
    </div>
</div>


</div>
<div class="overlay" id="overlay"></div>
<div class="my-bag" id="myBag">
     <div class="top-bag">
        <h1>MY Bag</h1>
        <i class="fa-solid fa-x x-icon" style="color: rgb(30, 48, 80);"></i>
     </div>

    
     <div class="mid-bag" id="midBag">
        <!-- Content injected by main.js -->
    </div>
</div>

</body>
</html>