<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$products = $db->fetchAll(
    'SELECT p.id, p.title, p.description, p.price, p.image_path, c.name AS category
     FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.created_at DESC'
);

$title = 'Products';
require __DIR__ . '/partials/header.php';
?>

<div class="page-content text-start">
  <h2>Products</h2>
</div>
<div class="row g-3">
  <?php foreach ($products as $p): ?>
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card h-100">
        <?php if (!empty($p['image_path'])): ?>
          <img src="<?= e($p['image_path']) ?>" class="card-img-top" alt="product">
        <?php endif; ?>
        <div class="card-body">
          <h5 class="card-title"><?= e($p['title']) ?></h5>
          <div class="text-muted small mb-2"><?= e($p['category'] ?? 'Pa kategori') ?></div>
          <?php if (!empty($p['description'])): ?><p class="card-text"><?= e($p['description']) ?></p><?php endif; ?>
        </div>
        <div class="card-footer d-flex justify-content-between">
          <span class="fw-bold"><?= $p['price'] !== null ? e((string)$p['price']) . ' €' : '' ?></span>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

