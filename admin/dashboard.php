<?php
session_start();
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — GearStore Admin</title>
  <link rel="stylesheet" href="../css/base.css" />
  <link rel="stylesheet" href="../css/admin.css" />
</head>
<body>

  <nav class="navbar">
    <div class="navbar-inner">
      <a href="../index.php" class="navbar-logo">GearStore</a>
      <ul class="navbar-links">
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
      </ul>
      <div class="navbar-right">
        <span style="font-size:0.85rem;color:#555"><?= htmlspecialchars($_SESSION['user_email']) ?></span>
        <a href="../logout.php" class="btn btn-outline btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="admin-wrap">
    <div class="admin-header">
      <h1>Products</h1>
      <a href="add.php" class="btn btn-dark">+ Add Product</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Discount</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p):
          $images = json_decode($p['image_urls'], true) ?? [];
          $img = $images[0] ?? '';
        ?>
          <tr>
            <td>
              <?php if ($img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:4px;" onerror="this.style.display='none'" />
              <?php else: ?>
                <span style="color:#ccc">—</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td>$<?= number_format($p['price'], 2) ?></td>
            <td><?= $p['discount'] ?>%</td>
            <td><?= $p['stock'] ?></td>
            <td>
              <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="delete.php?id=<?= $p['id'] ?>"
                 class="btn btn-outline btn-sm btn-danger"
                 onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
          <tr><td colspan="7" style="text-align:center;color:#999">No products yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
