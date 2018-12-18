<?php
require __DIR__ . "/../header.php";

use App\Authorization;

$auth = $container->get(Authorization::class);
$message = "";
if (!$auth->getIsGuest()){
    header("Location: index.php");
    return;
}
if(!empty($_POST['name']) && !empty($_POST['password'])) {
    $result = $auth->signInByPassword($_POST['name'], $_POST['password']);
    if ($result) {
        header("Location: index.php");
    } else {
        $message = "Wrong name or password";
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<?php require __DIR__ . "/../views/headers/header.php"; ?>
<div class="form-group w-50">
    <form action="login.php" method="post">
        <p>Name: <input class="form-control" type="text" name="name" /></p>
        <p>Password: <input class="form-control"type="text" name="password" /></p>
        <p><input class="form-control" type="submit" /></p>
    </form>
</div>
<div><?= $message ?></div>
</body>
</html>
