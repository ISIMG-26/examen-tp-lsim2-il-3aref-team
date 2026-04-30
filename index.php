<?php
session_start();
require 'db.php';

$cat  = $_GET['cat']  ?? 'All';
$sort = $_GET['sort'] ?? 'default';

$cats = $pdo->query("SELECT DISTINCT category FROM products WHERE LOWER(category) != 'tests' ORDER BY category")
            ->fetchAll(PDO::FETCH_COLUMN);

$sql      = "SELECT * FROM products WHERE LOWER(category) != 'tests'";
$stmt     = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($cat !== 'All') {
    $products = array_values(array_filter($products, fn($p) =>
        strtolower(trim($p['category'])) === strtolower(trim($cat))
    ));
}

if ($sort === 'price-asc')  usort($products, fn($a, $b) => $a['price'] <=> $b['price']);
if ($sort === 'price-desc') usort($products, fn($a, $b) => $b['price'] <=> $a['price']);
if ($sort === 'name')       usort($products, fn($a, $b) => strcmp($a['name'], $b['name']));

$count = count($products);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GearStore — Gaming Peripherals</title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/index.css" />
</head>
<body>

  <nav class="navbar">
    <div class="navbar-inner">
      <a href="index.php" class="navbar-logo">GearStore</a>
      <ul class="navbar-links">
        <li><a href="index.php" class="active">Shop</a></li>
      </ul>
      <div class="navbar-right">
        <a href="cart.html" class="cart-link">
          Cart
          <span class="cart-count" style="display:none">0</span>
        </a>
        <?php if (isset($_SESSION['user_role'])): ?>
          <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="admin/dashboard.php" class="btn btn-outline btn-sm">Dashboard</a>
          <?php endif; ?>
          <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline btn-sm">Sign In</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="hero">
    <h1>Gaming gear,<br>built to perform.</h1>
    <p>Mice, keyboards, headsets and more — all in one place.</p>
    <div class="hero-actions">
      <a href="#products" class="btn btn-dark">Browse Products</a>
    </div>
  </div>

  <div class="category-bar">
    <div class="category-bar-inner" id="cat-bar">
      <a href="?cat=All&sort=<?= urlencode($sort) ?>"
         class="cat-btn <?= $cat === 'All' ? 'active' : '' ?>">All</a>
      <?php foreach ($cats as $c): ?>
        <a href="?cat=<?= urlencode($c) ?>&sort=<?= urlencode($sort) ?>"
           class="cat-btn <?= strtolower($cat) === strtolower($c) ? 'active' : '' ?>">
          <?= htmlspecialchars($c) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="products-section" id="products">
    <div class="section-top">
      <h2><span id="result-count"><?= $count ?> product<?= $count !== 1 ? 's' : '' ?></span></h2>
      <form method="get" action="index.php">
        <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>" />
        <select name="sort" onchange="this.form.submit()">
          <option value="default"    <?= $sort === 'default'    ? 'selected' : '' ?>>Sort: Default</option>
          <option value="price-asc"  <?= $sort === 'price-asc'  ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="name"       <?= $sort === 'name'       ? 'selected' : '' ?>>Name: A–Z</option>
        </select>
      </form>
    </div>

    <div class="product-grid">
      <?php if ($count === 0): ?>
        <div class="empty-msg">No products found.</div>
      <?php else: ?>
        <?php foreach ($products as $p):
          $images       = json_decode($p['image_urls'], true) ?? [];
          $img          = $images[0] ?? '';
          $price        = (float) $p['price'];
          $discount     = (int)   $p['discount'];
          $final        = $discount > 0 ? $price * (1 - $discount / 100) : $price;
          $product_json = htmlspecialchars(json_encode([
            'id'       => (string) $p['id'],
            'name'     => $p['name'],
            'category' => $p['category'],
            'price'    => $price,
            'discount' => $discount,
            'image'    => $img,
          ]), ENT_QUOTES);
        ?>
          <div class="product-card" onclick="location.href='product.php?id=<?= $p['id'] ?>'">
            <img class="card-img" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                 loading="lazy" onerror="this.src='';this.style.background='#f0f0f0'" />
            <div class="card-body">
              <div class="card-category"><?= htmlspecialchars($p['category']) ?></div>
              <div class="card-name"><?= htmlspecialchars($p['name']) ?></div>
              <div class="card-desc"><?= htmlspecialchars($p['description'] ?? '') ?></div>
            </div>
            <div class="card-footer">
              <div>
                <span class="price">$<?= number_format($final, 2) ?></span>
                <?php if ($discount > 0): ?>
                  <span class="price-old">$<?= number_format($price, 2) ?></span>
                <?php endif; ?>
              </div>
              <?php if ((int) $p['stock'] === 0): ?>
                <span class="badge badge-out">Out of stock</span>
              <?php else: ?>
                <button class="btn btn-dark btn-sm add-btn"
                        data-product="<?= $product_json ?>"
                        onclick="event.stopPropagation()">Add</button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <footer>
    <p>&copy; 2026 GearStore &nbsp;·&nbsp; <a href="#">Privacy</a> &nbsp;·&nbsp; <a href="#">Terms</a></p>
  </footer>

  <script src="js/cart.js"></script>
  <script>
    document.querySelectorAll('.add-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var product = JSON.parse(btn.dataset.product);
        addToCart(product);
        btn.textContent = '✓';
        setTimeout(function() { btn.textContent = 'Add'; }, 800);
      });
    });
  </script>
</body>
</html>
