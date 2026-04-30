<?php
session_start();
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$id = $_GET['id'] ?? '';
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']        ?? '');
    $category    = trim($_POST['category']    ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price']    ?? 0);
    $discount    = (int)   ($_POST['discount'] ?? 0);
    $stock       = (int)   ($_POST['stock']    ?? 0);
    $image_urls  = trim($_POST['image_urls']  ?? '');

    if ($name === '' || $category === '' || $price <= 0) {
        $error = 'Name, category and price are required.';
    } else {
        $images = array_filter(array_map('trim', explode(',', $image_urls)));
        $images_json = json_encode(array_values($images));

        $stmt = $pdo->prepare('UPDATE products SET name=?, category=?, description=?, price=?, discount=?, stock=?, image_urls=? WHERE id=?');
        $stmt->execute([$name, $category, $description, $price, $discount, $stock, $images_json, $id]);
        header('Location: dashboard.php?msg=Product+updated');
        exit;
    }
}

$current_images = implode(', ', json_decode($p['image_urls'], true) ?? []);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Product — GearStore Admin</title>
  <link rel="stylesheet" href="../css/base.css" />
  <link rel="stylesheet" href="../css/admin.css" />
</head>
<body>

  <nav class="navbar">
    <div class="navbar-inner">
      <a href="../index.php" class="navbar-logo">GearStore</a>
      <ul class="navbar-links">
        <li><a href="dashboard.php">Dashboard</a></li>
      </ul>
      <div class="navbar-right">
        <a href="../logout.php" class="btn btn-outline btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="admin-wrap">
    <div class="admin-header">
      <h1>Edit Product #<?= $id ?></h1>
      <a href="dashboard.php" class="btn btn-outline">← Back</a>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="edit.php?id=<?= $id ?>" method="post" class="admin-form">

      <div class="field">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $p['name']) ?>" required />
      </div>

      <div class="field">
        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($_POST['category'] ?? $p['category']) ?>" required />
      </div>

      <div class="field">
        <label>Description</label>
        <input type="text" name="description" value="<?= htmlspecialchars($_POST['description'] ?? $p['description']) ?>" />
      </div>

      <div class="form-row">
        <div class="field">
          <label>Price ($)</label>
          <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($_POST['price'] ?? $p['price']) ?>" required />
        </div>
        <div class="field">
          <label>Discount (%)</label>
          <input type="number" name="discount" min="0" max="100" value="<?= htmlspecialchars($_POST['discount'] ?? $p['discount']) ?>" />
        </div>
        <div class="field">
          <label>Stock</label>
          <input type="number" name="stock" min="0" value="<?= htmlspecialchars($_POST['stock'] ?? $p['stock']) ?>" />
        </div>
      </div>

      <div class="field">
        <label>Image URLs (comma separated)</label>
        <input type="text" name="image_urls" value="<?= htmlspecialchars($_POST['image_urls'] ?? $current_images) ?>" />
      </div>

      <button type="submit" class="btn btn-dark">Save Changes</button>
    </form>
  </div>

</body>
</html>
