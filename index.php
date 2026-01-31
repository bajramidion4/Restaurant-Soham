<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$page = $db->fetchOne('SELECT title, body FROM pages WHERE slug = ?', ['home']);
$sliders = $db->fetchAll('SELECT image_path, caption FROM sliders ORDER BY sort_order ASC, id ASC');

$title = $page['title'] ?? 'Restaurant Soham';
require __DIR__ . '/partials/header.php';
?>

<div class="row g-4">
  <div class="col-12 text-start">
    <h2><?= e($page['title'] ?? 'Home') ?></h2>
    <div style="text-align: justify;"><?= nl2br(e($page['body'] ?? '')) ?></div>
  </div>

  <div class="col-12">
    <div id="sohamCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach ($sliders as $i => $s): ?>
          <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
            <img src="<?= e($s['image_path']) ?>" class="d-block w-100" alt="slide">
            <?php if (!empty($s['caption'])): ?>
              <div class="carousel-caption d-none d-md-block">
                <h5><?= e($s['caption']) ?></h5>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#sohamCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#sohamCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<?php require __DIR__ . '/partials/footer.php'; ?>

