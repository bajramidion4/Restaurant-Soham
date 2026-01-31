<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$page = $db->fetchOne('SELECT title, body FROM pages WHERE slug = ?', ['about']);
$title = $page['title'] ?? 'About';
require __DIR__ . '/partials/header.php';
?>

<div class="page-content text-start">
  <h2><?= e($page['title'] ?? 'About') ?></h2>
  <div class="about-body" style="text-align: justify;"><?= nl2br(e($page['body'] ?? '')) ?></div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

