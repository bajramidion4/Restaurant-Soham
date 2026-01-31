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

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("
        SELECT id, password_hash, role
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $error = "Email ose password gabim.";
    } else {
        // ✅ SESSIONET
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        header("Location: products.php");
        exit;
    }
}

$title = 'Login';
require __DIR__ . "/partials/header.php";
?>

<div class="container" style="max-width:500px">
  <h2>Login</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label>Email</label>
      <input class="form-control" name="email" type="email" required>
    </div>

    <div class="mb-3">
      <label>Password</label>
      <input class="form-control" name="password" type="password" required>
    </div>

    <button class="btn btn-dark">Login</button>
  </form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>
