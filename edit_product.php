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
  $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
  $stmt->bind_param("i", $id);
} else {
  $stmt = $conn->prepare(
    "SELECT * FROM products WHERE id = ? AND created_by = ?"
  );
  $stmt->bind_param("ii", $id, $userId);
}

$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) exit("Access denied.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $description = trim($_POST['description']);
  $price = (float)$_POST['price'];

  $stmt = $conn->prepare(
    "UPDATE products SET title=?, description=?, price=? WHERE id=?"
  );
  $stmt->bind_param("ssdi", $title, $description, $price, $id);
  $stmt->execute();

  header("Location: products.php");
  exit;
}

require __DIR__ . "/partials/header.php";
?>

<div class="container">
  <h2>Edit Product</h2>

  <form method="POST">
    <input class="form-control mb-2" name="title"
           value="<?= htmlspecialchars($product['title']) ?>" required>

    <textarea class="form-control mb-2" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

    <input class="form-control mb-3" type="number" step="0.01" name="price"
           value="<?= $product['price'] ?>" required>

    <button class="btn btn-warning">Update</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>
