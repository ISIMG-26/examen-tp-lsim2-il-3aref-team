<?php
$host   = 'localhost';
$dbname = 'shop';
$user   = 'root';
$pass   = 'ahmed';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
