<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/cart-lib.php';
$cart = cart_get();
$items = array_values($cart['items'] ?? []);
$totals = cart_totals();

function money_usd(int $cents): string {
    return '$' . number_format($cents / 100, 2);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="panier.css">
    <title>Panier</title>
</head>
<body>
    <div class="cart-page">
        <?php include("header.php"); ?>

        <main class="cart-shell">
            <div class="cart-head">
                <div>
                    <h1>Votre panier</h1>
                    <p class="cart-sub">Vérifiez vos articles avant de passer au paiement.</p>
                </div>
                <a class="cart-link" href="shop.php">Continuer le shopping</a>
            </div>

            <div class="cart-grid">
                <section class="cart-items">
                    <?php if (count($items) === 0): ?>
                        <div class="cart-empty">
                            <h3>Panier vide</h3>
                            <p>Ajoutez un produit pour commencer.</p>
                            <a class="btn-primary-solid" href="shop.php">Aller au shop</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($items as $it): ?>
                            <?php
                                $id = (string)($it['id'] ?? '');
                                $name = (string)($it['name'] ?? 'Item');
                                // $image = (string)($it['image'] ?? 'imgs/the-product.png');
                                $image = (string)($it['image'] ?? '' ?: 'imgs/the-product.png');
                                $image = str_replace('\\', '/', $image);

                                $qty = (int)($it['qty'] ?? 1);
                                $price = (int)($it['price'] ?? 0);
                                $line = $qty * $price;
                            ?>
                            <article class="cart-item" data-cart-row="<?php echo htmlspecialchars($id); ?>">
                                <div class="cart-item-img">
                                    <?php if ($image !== ''): ?>
                                        <img src="<?php echo htmlspecialchars($image); ?>" alt="">
                                    <?php endif; ?>
                                </div>
                                <div class="cart-item-main">
                                    <div class="cart-item-top">
                                        <div class="cart-item-name"><?php echo htmlspecialchars($name); ?></div>
                                        <button class="cart-remove" type="button" data-cart-remove="<?php echo htmlspecialchars($id); ?>">Supprimer</button>
                                    </div>
                                    <div class="cart-item-bottom">
                                        <div class="cart-qty">
                                            <button type="button" class="cart-qty-btn" data-cart-minus="<?php echo htmlspecialchars($id); ?>" data-cart-qty="<?php echo (int)$qty; ?>">−</button>
                                            <span class="cart-qty-value"><?php echo (int)$qty; ?></span>
                                            <button type="button" class="cart-qty-btn" data-cart-plus="<?php echo htmlspecialchars($id); ?>" data-cart-qty="<?php echo (int)$qty; ?>">+</button>
                                        </div>
                                        <div class="cart-line"><?php echo money_usd($line); ?></div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <div class="cart-actions">
                            <a class="btn-primary-solid" href="checkout.php">Checkout</a>
                            <a class="btn-ghost-link" href="shop.php">Continuer le shopping</a>
                        </div>
                    <?php endif; ?>
                </section>

                <aside class="cart-summary">
                    <div class="summary-card">
                        <h3>Résumé</h3>
                        <div class="summary-row">
                            <span>Sous-total</span>
                            <strong id="cartSubtotal"><?php echo money_usd((int)$totals['subtotal']); ?></strong>
                        </div>
                        <div class="summary-row muted">
                            <span>Livraison</span>
                            <span>Calculée au checkout</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <strong id="cartTotal"><?php echo money_usd((int)$totals['subtotal']); ?></strong>
                        </div>
                        <a class="btn-primary-solid" href="checkout.php">Passer au paiement</a>
                        <button class="btn-ghost" type="button" id="clearCartBtn">Vider le panier</button>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <script>
      (function(){
        const clearBtn = document.getElementById('clearCartBtn');
        clearBtn?.addEventListener('click', async () => {
          try{
            await fetch('cart-api.php?action=clear', { method: 'POST' });
            window.location.reload();
          }catch(e){
            alert('Impossible de vider le panier.');
          }
        });

        async function refreshTotals(){
          try{
            const res = await fetch('cart-api.php?action=get');
            const data = await res.json();
            if(!data?.ok) return;
            const subtotal = data.cart?.totals?.subtotal || 0;
            const money = (cents) => ((Number(cents)||0)/100).toLocaleString(undefined,{style:'currency',currency:'USD'});
            const subEl = document.getElementById('cartSubtotal');
            const totEl = document.getElementById('cartTotal');
            if(subEl) subEl.textContent = money(subtotal);
            if(totEl) totEl.textContent = money(subtotal);
          }catch(e){}
        }

        document.addEventListener('click', (e) => {
          if (e.target.closest('[data-cart-plus],[data-cart-minus],[data-cart-remove]')) {
            // main.js handles the API call, we just refresh totals after a short delay
            setTimeout(refreshTotals, 250);
          }
        });
      })();
    </script>
    <script src="main.js" defer></script>
</body>
</html>

