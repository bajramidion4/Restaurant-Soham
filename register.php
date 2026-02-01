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

class Register {
private mysqli $conn;

public function __construct(mysqli $conn) {
$this->conn = $conn;
}

public function register(string $name, string $email, string $password): array {
if (strlen($password) < 6) {
return ['ok' => false, 'error' => 'Password duhet me pasë së paku 6 karaktere'];
}

// kontrollo a ekziston email
$check = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
return ['ok' => false, 'error' => 'Ky email ekziston'];
}
$check->close();

$hash = password_hash($password, PASSWORD_DEFAULT);
$role = 'user';

$stmt = $this->conn->prepare("
INSERT INTO users (name, email, password_hash, role)
VALUES (?, ?, ?, ?)
");
$stmt->bind_param("ssss", $name, $email, $hash, $role);

if (!$stmt->execute()) {
return ['ok' => false, 'error' => 'Gabim gjatë regjistrimit'];
}

$stmt->close();
return ['ok' => true];
}
}

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$reg = new Register($conn);
$res = $reg->register($name, $email, $password);

if (!$res['ok']) {
$error = $res['error'];
} else {
$success = true;
}
}

$title = 'Register';
require __DIR__ . "/partials/header.php";
?>

<div class="container" style="max-width:500px">
<h2>Register</h2>

<?php if ($success): ?>
<div class="alert alert-success">
Regjistrimi u krye me sukses. <a href="login.php">Bëj login</a>
</div>
<?php elseif ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
<div class="mb-3">
<label>Emri</label>
<input class="form-control" name="name" required>
</div>

<div class="mb-3">
<label>Email</label>
<input class="form-control" name="email" type="email" required>
</div>

<div class="mb-3">
<label>Password</label>
<input class="form-control" name="password" type="password" required>
</div>

<button class="btn btn-dark">Register</button>
</form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>




