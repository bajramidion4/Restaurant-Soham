<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$items = $db->fetchAll(
    'SELECT n.id, n.title, n.body, n.media_path, n.created_at, u.name AS author
     FROM news n
     LEFT JOIN users u ON u.id = n.created_by
     ORDER BY n.created_at DESC'
);

$title = 'News';
require __DIR__ . '/partials/header.php';
?>

<div class="page-content text-start">
  <h2>News</h2>
</div>

<?php foreach ($items as $n): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title"><?= e($n['title']) ?></h5>
      <div class="text-muted small mb-2">
        <?= e($n['author'] ?? 'Anonim') ?> · <?= e((string)$n['created_at']) ?>
      </div>
      <p class="card-text"><?= nl2br(e($n['body'])) ?></p>
      <?php if (!empty($n['media_path'])): ?>
        <a class="btn btn-sm btn-outline-dark" href="<?= e($n['media_path']) ?>" target="_blank" rel="noreferrer">Hap file</a>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

