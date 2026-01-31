<?php
declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config.php';


require __DIR__ . '/src/Database.php';
require __DIR__ . '/src/Auth.php';
require __DIR__ . '/src/Helpers/helpers.php';

$db = new Database($config);
$auth = new Auth($db);
