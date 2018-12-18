<?php
use App\Authorization;

$container->get(Authorization::class)->signInByToken();
?>

<div class= "navbar navbar-expand-lg navbar-light bg-light">
    <a class="nav-link" href="index.php">Garden</a>
    <?php if ($container->get(Authorization::class)->getIsGuest()): ?>
    <a class="nav-link" href="login.php">Login</a>
    <a class="nav-link" href="registration.php">Register</a>
    <?php else: ?>
    <a class="nav-link" href="logout.php">Logout</a>
    <?php endif; ?>
</div>


