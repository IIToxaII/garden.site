<?php
require __DIR__ . "/../header.php";

use App\model\User;
use App\Authorization;
use App\model\Session;

    if (!$container->get(Authorization::class)->getIsGuest()){
        header('Location: index.php');
    }

    $page = '';
    if (!empty($_POST['name']) && !empty($_POST['password'])) {
        if (preg_match(" /^[a-z0-9_-]{3,16}$/", $_POST['name']) == 0) {
            $page .= 'Bad name<br/>';
        } elseif (preg_match("/^[a-zA-Z0-9!@#$%^&*]{6,20}$/", $_POST['password']) == 0) {
            $page .= 'Bad password<br/>';
        } else {
            $user = $container->make(User::class);
            $user->name = $_POST['name'];
            $user->setAndHashPassword($_POST['password']);
            $user->access_token = '';
            if ($user->save()) {
                $container->get(Authorization::class)->signInByUser($user);
                $session = $container->make(Session::class);
                $session->init($user);
                header('Location: index.php');
            } else {
                $page .= 'Name is taken<br/>';
            }
        }
    }
    echo $page;
?>

<html>
<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<?php require __DIR__ . "/../views/headers/header.php"; ?>
<div class="form-group w-50">
    <form action="registration.php" method="post">
        <p>Name: <input class="form-control" type="text" name="name" /></p>
        <p>Password: <input class="form-control" type="text" name="password" /></p>
        <p><input class="form-control" type="submit" /></p>
    </form>
</div>
</body>
</html>

