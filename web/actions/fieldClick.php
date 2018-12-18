<?php
require __DIR__ . "/../../header.php";

use App\Authorization;
use App\model\Session;
use App\model\Field;

$auth = $container->get(Authorization::class);

if (!$auth->getIsGuest()) {
    $session = $container->make(Session::class);
    $session->getByUser($auth->getUser());
    $result = $session->fieldClick($_POST['field_id']);
    $drawSymbol = Field::getDrawSymbol($result);
    echo json_encode([
        "symbol" => $drawSymbol,
        "money" => $session->money,
        "water" => $session->water,
        "seeds" => $session->seeds,
        "harvest" => $session->harvest]);
}
