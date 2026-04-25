<?php
require 'db.php';

$id   = $_GET['id'] ?? '';
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    $not_found = true;
} else {
    $images   = json_decode($p['image_urls'], true) ?? [];
    $price    = (float) $p['price'];
    $discount = (int)   $p['discount'];
    $final    = $discount > 0 ? $price * (1 - $discount / 100) : $price;
    $in_stock = (int) $p['stock'] > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($p) ? htmlspecialchars($p['name']) . ' — GearStore' : 'Product — GearStore' ?></title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/product.css" />
  <link rel="stylesheet" href="css/index.css" />
</head>
<body>

  <nav class="navbar">
    <div class="navbar-inner">
      <a href="index.php" class="navbar-logo">GearStore</a>
      <ul class="navbar-links">
        <li><a href="index.php">Shop</a></li>
      </ul>
      <div class="navbar-right">
        <a href="cart.html" class="cart-link">
          Cart
          <span class="cart-count" style="display:none">0</span>
        </a>
        <a href="login.html" class="btn btn-outline btn-sm">Sign In</a>
      </div>
    </div>
  </nav>

  <?php if (isset($not_found)): ?>
    <p style="padding:3rem;text-align:center;color:#888">
      Product not found. <a href="index.php">Back to shop</a>
    </p>
  <?php else: ?>

    <div class="breadcrumb">
      <a href="index.php">Home</a>
      /
      <a href="index.php?cat=<?= urlencode($p['category']) ?>"><?= htmlspecialchars($p['category']) ?></a>
      /
      <span><?= htmlspecialchars($p['name']) ?></span>
    </div>

    <div class="product-layout">

      <div>
        <div class="gallery-main">
          <img id="main-img" src="<?= htmlspecialchars($images[0] ?? '') ?>" alt="<?= htmlspecialchars($p['name']) ?>" />
        </div>
        <?php if (count($images) > 1): ?>
          <div class="gallery-thumbs">
            <?php foreach ($images as $i => $url): ?>
              <div class="thumb <?= $i === 0 ? 'active' : '' ?>"
                   onclick="switchImage(this, '<?= htmlspecialchars($url, ENT_QUOTES) ?>')">
                <img src="<?= htmlspecialchars($url) ?>" alt="" loading="lazy" />
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div>
        <div class="product-category"><?= htmlspecialchars($p['category']) ?></div>
        <h1 class="product-name"><?= htmlspecialchars($p['name']) ?></h1>
        <p class="product-desc"><?= htmlspecialchars($p['description'] ?? '') ?></p>

        <div class="price-block">
          <span class="price">$<?= number_format($final, 2) ?></span>
          <?php if ($discount > 0): ?>
            <span class="price-old">$<?= number_format($price, 2) ?></span>
          <?php endif; ?>
        </div>

        <?php if ($in_stock): ?>
          <div class="stock-line in-stock">In stock — <?= (int) $p['stock'] ?> available</div>
        <?php else: ?>
          <div class="stock-line out-stock">Out of stock</div>
        <?php endif; ?>

        <hr class="divider" />

        <div class="qty-row">
          <span class="qty-label">Qty</span>
          <div class="qty-ctrl">
            <button onclick="var i=document.getElementById('qty-input'); i.value=Math.max(1,+i.value-1)">−</button>
            <input type="number" id="qty-input" value="1" min="1" max="20" />
            <button onclick="var i=document.getElementById('qty-input'); i.value=Math.min(20,+i.value+1)">+</button>
          </div>
        </div>

        <div class="action-row">
          <button id="add-to-cart-btn" class="btn btn-dark" style="flex:1"
                  <?= !$in_stock ? 'disabled' : '' ?>>
            <?= $in_stock ? 'Add to Cart' : 'Out of stock' ?>
          </button>
        </div>

        <ul class="trust-list">
          <li>Free shipping on orders over $100</li>
          <li>30-day hassle-free returns</li>
          <li>1-year manufacturer warranty</li>
        </ul>
      </div>

    </div>

  <?php endif; ?>

  <footer>
    <p>&copy; 2026 GearStore &nbsp;·&nbsp; <a href="#">Privacy</a> &nbsp;·&nbsp; <a href="#">Terms</a></p>
  </footer>

  <script src="js/cart.js"></script>
  <?php if (!isset($not_found) && $in_stock): ?>
  <script>
    document.getElementById('add-to-cart-btn').addEventListener('click', function() {
      var qty = parseInt(document.getElementById('qty-input').value) || 1;
      for (var i = 0; i < qty; i++) {
        addToCart({
          id:       '<?= $p['id'] ?>',
          name:     '<?= addslashes($p['name']) ?>',
          category: '<?= addslashes($p['category']) ?>',
          price:    <?= $price ?>,
          discount: <?= $discount ?>,
          image:    '<?= addslashes($images[0] ?? '') ?>',
        });
      }
    });

    function switchImage(thumb, url) {
      document.querySelectorAll('.thumb').forEach(function(t) { t.classList.remove('active'); });
      thumb.classList.add('active');
      document.getElementById('main-img').src = url;
    }
  </script>
  <?php endif; ?>
</body>
</html>
