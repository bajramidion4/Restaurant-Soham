<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin   = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!doctype html>
<html lang="sq">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'Restaurant Soham' ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
</head>
<body>

<header class="mb-4">
  <h1 style="text-align:center;">Restaurant Soham</h1>

  <nav>
    <ul style="display:flex;gap:20px;list-style:none;justify-content:center;">
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="news.php">News</a></li>
      <li><a href="contact.php">Contact</a></li>

      <?php if (!$isLoggedIn): ?>
        <li><a href="login.php">Login</a></li>
      <?php else: ?>
        <?php if ($isAdmin): ?>
          <li><strong style="color:#ffd700;">Admin</strong></li>
        <?php endif; ?>
        <li><a href="logout.php">Logout</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<main class="page-main">
