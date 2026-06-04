<?php
$product = [
  'id' => '1',
  'name' => 'Supercharged Glow Complex (30ml)',
  'price_cents' => 8000,
  'price_label' => '80$',
  'images' => [
    'imgs/the-product.png',
    'imgs/large-img22.jpg',
    'imgs/large-img3.jpg',
  ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="shop.css?v=2">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='0' fill='%232A23BE'/><text y='72' font-size='60' font-family='Arial' font-weight='400' fill='white' text-anchor='middle' x='50'>CL</text></svg>">
  <title>Supercharged Glow Complex | CLshop.com</title>
</head>
<body>
  <?php include('header.php'); ?>

  <main class="shop-page" data-product-root>
    <div class="shop-page-left">
      <div class="left-side" id="thumbs">
        <?php foreach ($product['images'] as $idx => $src): ?>
          <img
            src="<?php echo htmlspecialchars($src); ?>"
            alt=""
            class="<?php echo $idx === 0 ? 'active' : ''; ?>"
            data-thumb-index="<?php echo (int)$idx; ?>"
          >
        <?php endforeach; ?>
      </div>

      <div class="right-side position-relative text-center">
        <img id="mainImage" src="<?php echo htmlspecialchars($product['images'][0]); ?>" class="img-fluid rounded" style="max-height: 600px; width:100%; object-fit:contain;" alt="">
              <button class="left-button" id="prevImgBtn" type="button" style="position:absolute; left:40%; bottom:45px; border:none; background:none; cursor:pointer;">
                <i class="fa-solid fa-arrow-left" style="color: rgb(36, 30, 198);"></i>
            </button>
                <button class="right-button" id="nextImgBtn" type="button" style="position:absolute; right:40%; bottom:45px; border:none; background:none; cursor:pointer;">
                <i class="fa-solid fa-arrow-right" style="color: rgb(36, 30, 198);"></i>
            </button>

        <div id="counter" style="position:absolute; bottom:45px; left:50%; transform:translateX(-50%);">
          1 / <?php echo count($product['images']); ?>
        </div>
      </div>
    </div>

    <div class="shop-page-right">
      <div class="infor">
        <p><i class="fa-solid fa-circle" style="color: rgb(36, 30, 198);"></i> Activate</p>
        <h1>Supercharged Glow <br> Complex</h1>
        <p>Your vitamin-rich solution for brighter, firmer, stronger skin. This
          multi-functional complex visibly fades post-acne marks and dark spots, smooths fine lines, and helps
          prevent damage from daily stressors, for skin that instantly looks more radiant.
        </p>
        <p>Powered by AB Science with TFC5™.</p>
        <p>Radiance _ Elasticity _ Damage Defense</p>
      </div>

      <div class="infor-down">
        <div class="size">
          <p>size</p>
          <p>30ml</p>
        </div>
        <div class="add-to-bag d-grid gap-2">
          <button
            class="btn btn-primary add-to-bag"
            type="button"
            data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
            data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
            data-product-price="<?php echo (int)$product['price_cents']; ?>"
            data-product-image="<?php echo htmlspecialchars($product['images'][0]); ?>"
          >ADD TO BAG - <?php echo htmlspecialchars($product['price_label']); ?></button>
        </div>
      </div>
    </div>
  </main>

  <script>
    (function () {
      const images = <?php echo json_encode(array_values($product['images'])); ?>;
      const mainImage = document.getElementById("mainImage");
      const counter = document.getElementById("counter");
      const thumbs = document.querySelectorAll("#thumbs img");
      const prevBtn = document.getElementById("prevImgBtn");
      const nextBtn = document.getElementById("nextImgBtn");

      if (!mainImage || !counter || !thumbs.length) return;

      let currentIndex = 0;

      function updateImage() {
        mainImage.src = images[currentIndex];
        counter.innerText = (currentIndex + 1) + " / " + images.length;
        thumbs.forEach((img, idx) => img.classList.toggle("active", idx === currentIndex));
      }

      thumbs.forEach((img) => {
        img.addEventListener("click", () => {
          const idx = Number(img.getAttribute("data-thumb-index") || 0);
          if (!Number.isFinite(idx)) return;
          currentIndex = Math.max(0, Math.min(images.length - 1, idx));
          updateImage();
        });
      });

      prevBtn?.addEventListener("click", () => {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateImage();
      });

      nextBtn?.addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % images.length;
        updateImage();
      });

     

      document.addEventListener("DOMContentLoaded", () => {
        updateImage();
        handleScroll();
      });
      window.addEventListener("scroll", handleScroll);
      window.addEventListener("resize", handleScroll);
    })();
  </script>
  <script src="main.js" defer></script>
</body>
</html>