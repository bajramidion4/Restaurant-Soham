<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) exit("Unauthorized");

$config = require __DIR__ . "/config.php";
$conn = new mysqli(
  $config['db']['host'],
  $config['db']['user'],
  $config['db']['pass'],
  $config['db']['name']
);

$id = (int)($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'admin') {
  $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
  $stmt->bind_param("i", $id);
} else {
  $stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ? AND created_by = ?"
  );
  $stmt->bind_param("ii", $id, $userId);
}

$stmt->execute();

header("Location: products.php");
exit;
