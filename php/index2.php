<!DOCTYPE html>
<html>
<head>
    <title>Stripe Test</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<h2>Test Payment</h2>
<button id="checkout-button">Pay $20</button>

<script>
var stripe = Stripe("pk_test_51TN94TCW2VopiZeHRlpfAhdAY8P8TjVFIv2UGLUwEBayk0oyCkqEKu4uI3OUeTpgAoGEge9OIsGsTVnhlnitOhCa007xryOPxG");

document.getElementById("checkout-button").addEventListener("click", function () {
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
</script>

</body>
</html>