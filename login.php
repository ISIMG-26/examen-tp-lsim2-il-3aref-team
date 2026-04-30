<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND password = ?');
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Wrong email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In — GearStore</title>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/login.css" />
</head>
<body>
  <div class="login-box">
    <div class="login-logo">GearStore</div>
    <div class="login-subtitle">Gaming peripherals &amp; gear</div>

    <h2>Sign in</h2>

    <?php if ($error): ?>
      <div style="background:#fff0f0;border:1px solid #fcc;color:#c00;padding:0.6rem 0.9rem;border-radius:5px;font-size:0.85rem;margin-bottom:1rem;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="post">
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" autocomplete="email" required />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
      </div>

      <button type="submit" class="btn btn-dark btn-full">Sign In</button>
    </form>

    <div class="login-footer" style="margin-top:1.2rem;">
      <a href="index.php">← Back to shop</a>
    </div>
  </div>
</body>
</html>
