<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$auth->logout();
redirect('index.php');

