<?php
session_start();
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$id = $_GET['id'] ?? '';

if ($id !== '') {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: dashboard.php?msg=Product+deleted');
exit;
