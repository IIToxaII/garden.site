<?php
require __DIR__ . "/../../header.php";

use App\Authorization;
use App\model\Session;

$auth = $container->get(Authorization::class);

if (!$auth->getIsGuest()) {
    $session = $container->make(Session::class);
    $session->getByUser($auth->getUser());
    $operation = $_POST['operation'];
    $session->$operation();
    echo json_encode([
        "money" => $session->money,
        "water" => $session->water,
        "seeds" => $session->seeds,
        "harvest" => $session->harvest]);
}