<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old']);
include("database.php");
require_once __DIR__ . '/cart-lib.php';
$cartPayload = cart_as_payload();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    if (empty(trim($_POST['email'] ?? '')))  $errors['email'] = 'Enter an email.';
    if (empty(trim($_POST['last-name'] ?? '')))  $errors['last-name'] = 'Enter a last name.';
    if (empty(trim($_POST['city']      ?? '')))  $errors['city']      = 'Enter a city.';
    if (empty(trim($_POST['address']   ?? '')))  $errors['address']   = 'Enter an address.';
    if (empty(trim($_POST['phone']     ?? '')))  $errors['phone']     = 'Enter a phone number.';

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = $_POST;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

}


$cartItems = $cartPayload['items'] ?? [];
$cartTotals = $cartPayload['totals'] ?? ['subtotal' => 0, 'count' => 0];

function money_usd_checkout(int $cents): string {
    return '$' . number_format($cents / 100, 2);
}

require_once __DIR__ . '/cart-lib.php';
$cart = cart_get();
$items = array_values($cart['items'] ?? []);

// Get first item (since you have 1 product)
$cart_item = $items[0] ?? null;
$product_id = (int)($cart_item['id'] ?? 1);
$quantity   = (int)($cart_item['qty'] ?? 1);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="checkout.css">
    <!-- <link rel="icon" type="image/png" href="CL_favicon.svg"> -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='0' fill='%232A23BE'/><text y='72' font-size='60' font-family='Arial' font-weight='400' fill='white' text-anchor='middle' x='50'>CL</text></svg>">
      <script src="https://js.stripe.com/v3/"></script>
    <title>Checkout - CLshop.com</title>
</head>
<body>
    <div class="checkout-page">
        <?php
        $HEADER_DISABLE_BOOTSTRAP = true;
        include("header.php")
        ?>
        <div class="checkout-left">
       <form action="checkout.php" method="POST" class="checkout">
           
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
<input type="hidden" name="quantity"   value="<?php echo $quantity; ?>">
  <!-- Contact -->
  <h3>Contact</h3>
  <?php
    $sessionEmail = isset($_SESSION['email']) ? trim((string)$_SESSION['email']) : '';
    if ($sessionEmail !== ''):
  ?>
      <div class="input-group full">
        <input type="email" name="email" value="<?php echo htmlspecialchars($sessionEmail); ?>" readonly placeholder=" ">
        <label>Email</label>
      </div>
  <?php else: ?>
      <div class="input-group full">
        <input type="email" name="email" placeholder=" "
      value="<?= htmlspecialchars($old['email'] ?? '') ?>"
      class="<?= isset($errors['email']) ? 'error' : '' ?>"
        >
        <label>Email</label>
        <p class="error-msg">
        <?= $errors['last-name'] ?? '' ?>
</p>
      </div>
  <?php endif; ?>

  <!-- Delivery -->
  <h3>Delivery</h3>
  <div class="input-group">
  <select  name="wilaya">
   

    <option>01 - Adrar</option>
    <option>02 - Chlef</option>
    <option>03 - Laghouat</option>
    <option>04 - Oum El Bouaghi</option>
    <option>05 - Batna</option>
    <option>06 - Béjaïa</option>
    <option>07 - Biskra</option>
    <option>08 - Béchar</option>
    <option>09 - Blida</option>
    <option>10 - Bouira</option>
    <option>11 - Tamanrasset</option>
    <option>12 - Tébessa</option>
    <option>13 - Tlemcen</option>
    <option>14 - Tiaret</option>
    <option>15 - Tizi Ouzou</option>
    <option selected>16 - Alger</option>
    <option>17 - Djelfa</option>
    <option>18 - Jijel</option>
    <option>19 - Sétif</option>
    <option>20 - Saïda</option>
    <option>21 - Skikda</option>
    <option>22 - Sidi Bel Abbès</option>
    <option>23 - Annaba</option>
    <option>24 - Guelma</option>
    <option>25 - Constantine</option>
    <option>26 - Médéa</option>
    <option>27 - Mostaganem</option>
    <option>28 - M'Sila</option>
    <option>29 - Mascara</option>
    <option>30 - Ouargla</option>
    <option>31 - Oran</option>
    <option>32 - El Bayadh</option>
    <option>33 - Illizi</option>
    <option>34 - Bordj Bou Arréridj</option>
    <option>35 - Boumerdès</option>
    <option>36 - El Tarf</option>
    <option>37 - Tindouf</option>
    <option>38 - Tissemsilt</option>
    <option>39 - El Oued</option>
    <option>40 - Khenchela</option>
    <option>41 - Souk Ahras</option>
    <option>42 - Tipaza</option>
    <option>43 - Mila</option>
    <option>44 - Aïn Defla</option>
    <option>45 - Naâma</option>
    <option>46 - Aïn Témouchent</option>
    <option>47 - Ghardaïa</option>
    <option>48 - Relizane</option>
    <option>49 - Timimoun</option>
    <option>50 - Bordj Badji Mokhtar</option>
    <option>51 - Ouled Djellal</option>
    <option>52 - Béni Abbès</option>
    <option>53 - In Salah</option>
    <option>54 - In Guezzam</option>
    <option>55 - Touggourt</option>
    <option>56 - Djanet</option>
    <option>57 - El M'Ghair</option>
    <option>58 - El Meniaa</option>
</select>
<label>Wilaya</label>
</div>

<div class="form-row full">
  <div class="input-group">
    <input type="text" placeholder=" " name="last-name" 
 
      value="<?= htmlspecialchars($old['last-name'] ?? '') ?>"
      class="<?= isset($errors['last-name']) ? 'error' : '' ?>">
       <label>Last name</label>
   <p class="error-msg">
  <?= $errors['last-name'] ?? '' ?>
</p>
  </div>
  
  <div class="input-group">
    <input type="text" placeholder="" name="first-name">
    <label>First name (optional)</label>
  </div>

</div>

<div class="input-group full">
  <input type="text"  placeholder=" " name="address"
  value="<?= htmlspecialchars($old['address'] ?? '') ?>"
    class="<?= isset($errors['address']) ? 'error' : '' ?>">
  <label>Address</label>
  <p class="error-msg">
  <?= $errors['address'] ?? '' ?>
</p>
</div>

<div class="input-group full">
  <input type="text" placeholder=" " name="apartment">
  <label>Apartment, suite, etc. (optional)</label>
</div>

<div class="input-group full">
  <input type="text"  placeholder=" " name="city"
  value="<?= htmlspecialchars($old['city'] ?? '') ?>"
    class="<?= isset($errors['city']) ? 'error' : '' ?>"> 
  <label>City</label>
  <p class="error-msg">
  <?= $errors['city'] ?? '' ?>
</p>
</div>

<div class="input-group full">
  <input type="text"  placeholder=" " name="phone"
  value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
    class="<?= isset($errors['phone']) ? 'error' : '' ?>">
  <label>Phone</label>
  <p class="error-msg">
  <?= $errors['phone'] ?? '' ?>
</p>
</div>


  <button id="checkout-button" name="pay-now" type="submit" class="pay-btn" >
    Pay now
  </button>

</form>


</div>

  <div class="checkout-right">
    <div class="order-card">
      <h3>Order summary</h3>
      <?php if (!is_array($cartItems) || count($cartItems) === 0): ?>
        <div class="order-empty">
          <p>Your cart is empty.</p>
          <a class="order-link" href="shop.php">Go to shop</a>
        </div>
      <?php else: ?>
        <div class="order-items">
          <?php foreach ($cartItems as $it): ?>
            <?php
              $name = (string)($it['name'] ?? 'Item');
              $qty = (int)($it['qty'] ?? 1);
              $price = (int)($it['price'] ?? 0);
              $img = (string)($it['image'] ?? '');
              $line = $qty * $price;
            ?>
            <div class="order-item">
              <div class="order-img">
                <?php if ($img !== ''): ?>
                  <img src="<?php echo htmlspecialchars($img); ?>" alt="">
                <?php endif; ?>
              </div>
              <div class="order-main">
                <div class="order-name"><?php echo htmlspecialchars($name); ?></div>
                <div class="order-meta">Qty: <?php echo (int)$qty; ?></div>
              </div>
              <div class="order-price"><?php echo money_usd_checkout((int)$line); ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="order-totals">
          <div class="order-row">
            <span>Subtotal</span>
            <strong><?php echo money_usd_checkout((int)($cartTotals['subtotal'] ?? 0)); ?></strong>
          </div>
          <div class="order-row muted">
            <span>Shipping</span>
            <span>Calculated at checkout</span>
          </div>
          <div class="order-total">
            <span>Total</span>
            <strong><?php echo money_usd_checkout((int)($cartTotals['subtotal'] ?? 0)); ?></strong>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
    </div>

<!--     
<script>
var stripe = Stripe("pk_test_51TN94TCW2VopiZeHRlpfAhdAY8P8TjVFIv2UGLUwEBayk0oyCkqEKu4uI3OUeTpgAoGEge9OIsGsTVnhlnitOhCa007xryOPxG");

document.getElementById("checkout-button").addEventListener("click", function (e) {
    e.preventDefault();
    if (this.disabled) return;


    let hasError = false;

    const requiredField = [
        { name:  'email', msg: 'Enter an email'},
        { name: 'last-name',  msg: 'Enter a last name.' },
        { name: 'city',       msg: 'Enter a city.' },
        { name: 'address',    msg: 'Enter an address.' },
        { name: 'phone',      msg: 'Enter a phone number.' },
    ];

    requiredField.forEach(({ name, msg }) => {
        const input = document.querySelector(`input[name="${name}"]`);
        const errorMsg = input.closest('.input-group').querySelector('.error-msg');
        if (input.value.trim() === '') {
            input.classList.add('error');
            errorMsg.textContent = msg;
            hasError = true;
        }
    });

    if (hasError) return;

   
    const requiredFields = document.querySelectorAll('input[required], select[required]');
    for (let field of requiredFields) {
        if (field.value.trim() === '') {
            alert('Please fill in all required fields.');
            field.focus();
            return;
        }
    }

    fetch("checkout-stripe.php", { method: "POST" })
        .then(response => response.json())
        .then(session => {
            return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .then(result => {
            if (result.error) {
                alert(result.error.message);
            }
        });
});
</script>  -->

<script>
var stripe = Stripe("pk_test_51TN94TCW2VopiZeHRlpfAhdAY8P8TjVFIv2UGLUwEBayk0oyCkqEKu4uI3OUeTpgAoGEge9OIsGsTVnhlnitOhCa007xryOPxG");

document.getElementById("checkout-button").addEventListener("click", function (e) {
    e.preventDefault();
    if (this.disabled) return;

    let hasError = false;

    const requiredField = [
        { name: 'email',     msg: 'Enter an email.' },
        { name: 'last-name', msg: 'Enter a last name.' },
        { name: 'city',      msg: 'Enter a city.' },
        { name: 'address',   msg: 'Enter an address.' },
        { name: 'phone',     msg: 'Enter a phone number.' },
    ];

    requiredField.forEach(({ name, msg }) => {
        const input = document.querySelector(`input[name="${name}"]`);
        const errorMsg = input.closest('.input-group').querySelector('.error-msg');
        if (input.value.trim() === '') {
            input.classList.add('error');
            errorMsg.textContent = msg;
            hasError = true;
        }
    });

    if (hasError) return;

    const formData = new FormData(document.querySelector('form.checkout'));
    fetch("checkout-stripe.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(session => stripe.redirectToCheckout({ sessionId: session.id }))
    .then(result => { if (result.error) alert(result.error.message); });

}); // <-- this was missing!
</script>
<script src="main.js" defer></script>

<!-- here -->


<!-- <script>
document.addEventListener('input', function(e) {
  if (e.target.name === 'last-name') {
    const input = e.target;
    const errorMsg = input.closest('.input-group').querySelector('.error-msg');

    if (input.value.trim() === '') {
      input.classList.add('error');
      errorMsg.textContent = 'Please enter your last name.';
      btn.disabled = true;
    } else {
      input.classList.remove('error');
      errorMsg.textContent = '';
      btn.disabled = false;
    }
  }
});
</script>  -->

<script>
document.addEventListener('input', function(e) {
    const watchedFields = ['email','last-name', 'city', 'address', 'phone'];
    if (!watchedFields.includes(e.target.name)) return;

    const input = e.target;
    const errorMsg = input.closest('.input-group').querySelector('.error-msg');

    if (input.value.trim() === '') {
        input.classList.add('error');
        errorMsg.textContent = e.target.getAttribute('data-error') || 'This field is required.';
    } else {
        input.classList.remove('error');
        errorMsg.textContent = '';
    }
});
</script>
</body>
</html>


