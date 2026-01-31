<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

if ($auth->check()) {
    redirect($auth->isAdmin() ? 'admin/index.php' : 'index.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['_csrf'] ?? null)) {
        $error = 'CSRF error. Rifresko faqen.';
    } else {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        if (!$auth->login($email, $password)) {
            $error = 'Email ose password gabim.';
        } else {
            redirect($auth->isAdmin() ? 'admin/index.php' : 'index.php');
        }
    }
}

$title = 'Login';
require __DIR__ . '/partials/header.php';
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-6">
    <h2>Login</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="vstack gap-3">
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
      <div>
        <label class="form-label">Email</label>
        <input class="form-control" name="email" type="email" required>
      </div>
      <div>
        <label class="form-label">Password</label>
        <input class="form-control" name="password" type="password" required>
      </div>
      <button class="btn btn-dark" type="submit">Login</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

