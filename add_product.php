<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$config = require __DIR__ . "/config.php";
$conn = new mysqli(
  $config['db']['host'],
  $config['db']['user'],
  $config['db']['pass'],
  $config['db']['name']
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $description = trim($_POST['description']);
  $price = (float)$_POST['price'];
  $category_id = (int)$_POST['category_id'];
  $user_id = $_SESSION['user_id'];

  $stmt = $conn->prepare(
    "INSERT INTO products (title, description, price, category_id, created_by)
     VALUES (?, ?, ?, ?, ?)"
  );
  $stmt->bind_param("ssdii", $title, $description, $price, $category_id, $user_id);
  $stmt->execute();

  header("Location: products.php");
  exit;
}

require __DIR__ . "/partials/header.php";
?>

<div class="container">
  <h2>Add Product</h2>

  <form method="POST">
    <input class="form-control mb-2" name="title" placeholder="Title" required>
    <textarea class="form-control mb-2" name="description" placeholder="Description" required></textarea>
    <input class="form-control mb-2" type="number" step="0.01" name="price" placeholder="Price" required>
    <input class="form-control mb-3" type="number" name="category_id" placeholder="Category ID" required>

    <button class="btn btn-danger">Save</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>
