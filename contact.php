<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$page = $db->fetchOne('SELECT title, body FROM pages WHERE slug = ?', ['contact']);
$title = $page['title'] ?? 'Contact';

$ok = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['_csrf'] ?? null)) {
        $error = 'CSRF error. Rifresko faqen.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        if ($name === '' || $email === '' || $message === '') {
            $error = 'Plotëso të gjitha fushat.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email jo valid.';
        } else {
            $db->exec(
                'INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)',
                [$name, $email, $message]
            );
            $ok = true;
        }
    }
}

require __DIR__ . '/partials/header.php';
?>

<div class="page-content text-start">
  <h2><?= e($page['title'] ?? 'Contact') ?></h2>
  <div class="contact-intro" style="text-align: justify; margin-bottom: 1.5rem;"><?= nl2br(e($page['body'] ?? '')) ?></div>

<?php if ($ok): ?>
  <div class="alert alert-success">Mesazhi u dërgua.</div>
<?php elseif ($error): ?>
  <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" class="row g-3">
  <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
  <div class="col-12 col-md-6">
    <label class="form-label">Emri</label>
    <input class="form-control" name="name" required>
  </div>
  <div class="col-12 col-md-6">
    <label class="form-label">Email</label>
    <input class="form-control" name="email" type="email" required>
  </div>
  <div class="col-12">
    <label class="form-label">Mesazhi</label>
    <textarea class="form-control" name="message" rows="4" required></textarea>
  </div>
  <div class="col-12">
    <button class="btn btn-dark" type="submit">Dërgo</button>
  </div>
</form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

