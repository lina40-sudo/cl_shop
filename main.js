const bag = document.getElementById("myBag");
const overlay = document.getElementById("overlay");
const midBag = document.getElementById("midBag");
const bagCountEl = document.getElementById("bagCount");

function formatMoney(cents) {
  const value = (Number(cents) || 0) / 100;
  return value.toLocaleString(undefined, { style: "currency", currency: "USD" });
}

async function cartRequest(action, payload = {}) {
  const res = await fetch(`cart-api.php?action=${encodeURIComponent(action)}`, {
    method: action === "get" ? "GET" : "POST",
    headers: action === "get" ? undefined : { "Content-Type": "application/json" },
    body: action === "get" ? undefined : JSON.stringify(payload),
  });
  const data = await res.json().catch(() => null);
  if (!data || !data.ok) {
    const message = data?.error || "Cart error";
    throw new Error(message);
  }
  return data.cart;
}

// =======================
// OPEN BAG
// =======================
function openBag(event) {
    if (event) event.preventDefault();

    bag?.classList.add("active");
    overlay?.classList.add("active");
    document.body.classList.add("no-scroll");
    renderBag();
}

// =======================
// CLOSE BAG
// =======================
function closeBag() {
    bag?.classList.remove("active");
    overlay?.classList.remove("active");
    document.body.classList.remove("no-scroll");
}

// =======================
// CLOSE EVENTS
// =======================
overlay?.addEventListener("click", closeBag);

// close icon (X)
document.addEventListener("click", function (e) {
    if (e.target.closest(".x-icon")) {
        closeBag();
    }
});


// =======================
// OPEN BAG FROM BUTTONS (ANY PAGE)
// =======================
document.addEventListener("click", function (e) {
    // ADD TO BAG button
    if (e.target.closest(".add-to-bag")) {
        openBag(e);
    }

    // BAG ICON (if you use class for it)
    if (e.target.closest(".bag-icon")) {
        openBag(e);
    }
});
document.addEventListener("click", async function (e) {
  const addBtn = e.target.closest(".add-to-bag");
  if (addBtn) {
    e.preventDefault();
    const id = addBtn.getAttribute("data-product-id") || "item";
    const name = addBtn.getAttribute("data-product-name") || "Item";
    const price = Number(addBtn.getAttribute("data-product-price") || 0);
    const image = addBtn.getAttribute("data-product-image") || "";

    try {
      await cartRequest("add", { id, name, price, image, qty: 1 });
      openBag();
      await renderBag();
    } catch (err) {
      console.error(err);
      alert("Impossible d'ajouter au panier.");
    }
    return;
  }

  const plus = e.target.closest("[data-cart-plus]");
  if (plus) {
    const id = plus.getAttribute("data-cart-plus");
    const current = Number(plus.getAttribute("data-cart-qty") || 1) + 1;
    await cartRequest("setQty", { id, qty: current });
    await renderBag();
    return;
  }

  const minus = e.target.closest("[data-cart-minus]");
  if (minus) {
    const id = minus.getAttribute("data-cart-minus");
    const current = Number(minus.getAttribute("data-cart-qty") || 1) - 1;
    await cartRequest("setQty", { id, qty: current });
    await renderBag();
    return;
  }

  const remove = e.target.closest("[data-cart-remove]");
  if (remove) {
    const id = remove.getAttribute("data-cart-remove");
    await cartRequest("remove", { id });
    await renderBag();
    return;
  }
});

async function refreshBagCount() {
  try {
    const cart = await cartRequest("get");
    const count = cart?.totals?.count ?? 0;
    if (bagCountEl) bagCountEl.textContent = String(count);
  } catch {
    if (bagCountEl) bagCountEl.textContent = "0";
  }
}

async function renderBag() {
  if (!midBag) return;

  let cart;
  try {
    cart = await cartRequest("get");
  } catch {
    midBag.innerHTML = `
      <div class="bag-empty">
        <h3>Votre panier est vide</h3>
        <p>Ajoutez un produit pour commencer.</p>
      </div>
    `;
    return;
  }

  const items = cart.items || [];
  const subtotal = cart?.totals?.subtotal ?? 0;
  const count = cart?.totals?.count ?? 0;
  if (bagCountEl) bagCountEl.textContent = String(count);

  if (!items.length) {
    midBag.innerHTML = `
      <div class="bag-empty">
        <h3>Votre panier est vide</h3>
        <p>Ajoutez un produit pour commencer.</p>
      </div>
    `;
    return;
  }

  const rows = items
    .map((it) => {
      const line = (Number(it.price) || 0) * (Number(it.qty) || 0);
      const img = it.image ? `<img class="bag-item-img" src="${it.image}" alt="">` : "";
      return `
        <div class="bag-item">
          ${img}
          <div class="bag-item-main">
            <div class="bag-item-top">
              <div class="bag-item-name">${it.name}</div>
              <button class="bag-item-remove" type="button" data-cart-remove="${it.id}" aria-label="Remove">×</button>
            </div>
            <div class="bag-item-bottom">
              <div class="bag-qty">
                <button type="button" class="bag-qty-btn" data-cart-minus="${it.id}" data-cart-qty="${it.qty}">−</button>
                <span class="bag-qty-value">${it.qty}</span>
                <button type="button" class="bag-qty-btn" data-cart-plus="${it.id}" data-cart-qty="${it.qty}">+</button>
              </div>
              <div class="bag-item-price">${formatMoney(line)}</div>
            </div>
          </div>
        </div>
      `;
    })
    .join("");

  midBag.innerHTML = `
    <div class="bag-items">${rows}</div>
    <div class="bag-summary">
      <div class="bag-summary-row">
        <span>Subtotal</span>
        <strong>${formatMoney(subtotal)}</strong>
      </div>
      <div class="bag-summary-actions">
        <a class="bag-btn bag-btn-secondary" href="panier.php">Voir le panier</a>
        <a class="bag-btn bag-btn-primary" href="checkout.php">Checkout</a>
      </div>
    </div>
  `;
}

refreshBagCount();


let lastScroll = 0;

window.addEventListener('scroll', function() {
    const headers = document.querySelectorAll('.header-page1');
    const currentScroll = document.documentElement.scrollTop || document.body.scrollTop;

    headers.forEach(header => {
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        if (currentScroll > lastScroll && currentScroll > 200) {
            header.classList.add('hidden');
        } else {
            header.classList.remove('hidden');
        }
    });

    lastScroll = currentScroll;
});
