<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

if ($auth->check()) {
    redirect('index.php');
}

$error = null;
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['_csrf'] ?? null)) {
        $error = 'CSRF error. Rifresko faqen.';
    } else {
        $res = $auth->register(
            (string)($_POST['name'] ?? ''),
            (string)($_POST['email'] ?? ''),
            (string)($_POST['password'] ?? '')
        );
        if (!$res['ok']) {
            $error = (string)$res['error'];
        } else {
            $ok = true;
        }
    }
}

$title = 'Register';
require __DIR__ . '/partials/header.php';
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-6">
    <h2>Register</h2>
    <?php if ($ok): ?>
      <div class="alert alert-success">U regjistrua me sukses. Tani bëj login.</div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="vstack gap-3">
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
      <div>
        <label class="form-label">Emri</label>
        <input class="form-control" name="name" required>
      </div>
      <div>
        <label class="form-label">Email</label>
        <input class="form-control" name="email" type="email" required>
      </div>
      <div>
        <label class="form-label">Password</label>
        <input class="form-control" name="password" type="password" required>
      </div>
      <button class="btn btn-dark" type="submit">Register</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

