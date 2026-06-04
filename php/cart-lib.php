<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Cart stored in $_SESSION['cart'] as:
 * [
 *   'items' => [
 *      '<id>' => ['id'=>string,'name'=>string,'price'=>int,'qty'=>int,'image'=>string]
 *   ]
 * ]
 */
function cart_init(): void {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = ['items' => []];
    }
    if (!isset($_SESSION['cart']['items']) || !is_array($_SESSION['cart']['items'])) {
        $_SESSION['cart']['items'] = [];
    }
}

function cart_get(): array {
    cart_init();
    return $_SESSION['cart'];
}

function cart_set_item(array $item): void {
    cart_init();
    $id = (string)($item['id'] ?? '');
    if ($id === '') return;

    $name = (string)($item['name'] ?? 'Item');
    $price = (int)($item['price'] ?? 0); // cents
    $qty = (int)($item['qty'] ?? 1);
    $image = (string)($item['image'] ?? '');

    if ($price < 0) $price = 0;
    if ($qty < 1) $qty = 1;
    if ($qty > 99) $qty = 99;

    $_SESSION['cart']['items'][$id] = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'qty' => $qty,
        'image' => $image,
    ];
}

function cart_add(string $id, string $name, int $price, string $image = '', int $qty = 1): void {
    cart_init();
    if ($id === '') return;
    if ($qty < 1) $qty = 1;
    if ($qty > 99) $qty = 99;

    if (isset($_SESSION['cart']['items'][$id])) {
        $current = (int)($_SESSION['cart']['items'][$id]['qty'] ?? 0);
        $newQty = $current + $qty;
        if ($newQty > 99) $newQty = 99;
        $_SESSION['cart']['items'][$id]['qty'] = $newQty;
        return;
    }

    cart_set_item([
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'qty' => $qty,
        'image' => $image,
    ]);
}

function cart_update_qty(string $id, int $qty): void {
    cart_init();
    if (!isset($_SESSION['cart']['items'][$id])) return;
    if ($qty <= 0) {
        unset($_SESSION['cart']['items'][$id]);
        return;
    }
    if ($qty > 99) $qty = 99;
    $_SESSION['cart']['items'][$id]['qty'] = $qty;
}

function cart_remove(string $id): void {
    cart_init();
    unset($_SESSION['cart']['items'][$id]);
}

function cart_clear(): void {
    $_SESSION['cart'] = ['items' => []];
}

function cart_totals(): array {
    cart_init();
    $items = array_values($_SESSION['cart']['items']);
    $count = 0;
    $subtotal = 0;
    foreach ($items as $it) {
        $qty = (int)($it['qty'] ?? 0);
        $price = (int)($it['price'] ?? 0);
        if ($qty < 0) $qty = 0;
        if ($price < 0) $price = 0;
        $count += $qty;
        $subtotal += ($qty * $price);
    }
    return [
        'count' => $count,
        'subtotal' => $subtotal, // cents
        'currency' => 'usd',
    ];
}

function cart_as_payload(): array {
    cart_init();
    $items = array_values($_SESSION['cart']['items']);
    $totals = cart_totals();
    return [
        'items' => $items,
        'totals' => $totals,
    ];
}

