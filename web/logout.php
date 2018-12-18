<?php
require __DIR__ . "/../header.php";

use App\Authorization;

$auth = $container->get(Authorization::class);
$auth->logout();
header('Location: index.php');
?>