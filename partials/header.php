<?php
/** @var Auth $auth */
$inAdmin = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') !== false);
$baseCss = $inAdmin ? '../index.css' : 'index.css';
$cssVer = filemtime(__DIR__ . '/../index.css') ?: time();
$baseHref = $inAdmin ? '../' : '';
?>
<!doctype html>
<html lang="sq">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Restaurant Soham') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= e($baseCss) ?>?v=<?= $cssVer ?>">
<style>
/* Faqet PHP - bardhë dhe e zezë - inline për ta garantuar */
html,body{background:#fff!important;color:#000!important}
main.page-main{background:#fff!important;background-color:#fff!important;color:#000!important}
main.page-main,main.page-main *{color:#000!important}
main.page-main h1,main.page-main h2,main.page-main h3,main.page-main h4,main.page-main h5,main.page-main p,main.page-main div,main.page-main span{color:#000!important}
</style>
</head>
<body>
<header class="mb-4">
  <marquee><h1>Shtypja e porosive bëhet këtu</h1></marquee>
  <nav>
    <ul>
      <li><a href="<?= $baseHref ?>index.php">Home</a></li>
      <li><a href="<?= $baseHref ?>about.php">About</a></li>
      <li><a href="<?= $baseHref ?>products.php">Products</a></li>
      <li><a href="<?= $baseHref ?>news.php">News</a></li>
      <li><a href="<?= $baseHref ?>contact.php">Contact</a></li>
      <?php if ($auth->check()): ?>
        <?php if ($auth->isAdmin()): ?><li><a href="<?= $inAdmin ? 'index.php' : 'admin/index.php' ?>">Dashboard</a></li><?php endif; ?>
        <li><a href="<?= $baseHref ?>logout.php">Logout</a></li>
      <?php else: ?>
        <li><a href="<?= $baseHref ?>login.php">Login</a></li>
        <li><a href="<?= $baseHref ?>register.php">Register</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>
<main class="page-main" style="background:#fff!important;color:#000!important">
<div class="container">

