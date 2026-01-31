<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$config = require __DIR__ . "/config.php";

$conn = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}

$isLoggedIn = isset($_SESSION['user_id']);
$userId    = $_SESSION['user_id'] ?? null;
$isAdmin   = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$sql = "
  SELECT p.*, c.name AS category
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.id
  ORDER BY p.created_at DESC
";
$result = $conn->query($sql);

$title = 'Products';
require __DIR__ . "/partials/header.php";
?>

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Products</h2>

    <?php if ($isLoggedIn): ?>
      <a href="add_product.php" class="btn btn-danger">+ Add Product</a>
    <?php endif; ?>
  </div>

  <?php if ($result->num_rows === 0): ?>
    <p>No products found.</p>
  <?php else: ?>
    <div class="d-flex flex-wrap gap-4">

      <?php while ($p = $result->fetch_assoc()): ?>
        <div class="card" style="width:18rem;">
          <div class="card-body">

            <h5><?= htmlspecialchars($p['title']) ?></h5>
            <p><?= htmlspecialchars($p['description']) ?></p>
            <p><strong>€<?= number_format((float)$p['price'], 2) ?></strong></p>

            <?php if ($p['category']): ?>
              <small>Category: <?= htmlspecialchars($p['category']) ?></small>
            <?php endif; ?>

            <?php if ($isLoggedIn && ($isAdmin || $p['created_by'] == $userId)): ?>
              <div class="mt-3 d-flex gap-2">
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_product.php?id=<?= $p['id'] ?>"
                   onclick="return confirm('A je i sigurt?')"
                   class="btn btn-sm btn-danger">Delete</a>
              </div>
            <?php endif; ?>

          </div>
        </div>
      <?php endwhile; ?>

    </div>
  <?php endif; ?>

</div>

<?php require __DIR__ . "/partials/footer.php"; ?>
