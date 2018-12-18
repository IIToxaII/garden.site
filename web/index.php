<?php
require __DIR__ . "/../header.php";

use App\Authorization;

if ($container->get(Authorization::class)->getIsGuest()) {
    header("Location: login.php");
}

$session = $container->make(\App\model\Session::class);;
$session->getByUser($container->get(Authorization::class)->getUser());
$array = $session->getAllFields();
$currentX = $array[0]->position_x;
$table = '<tr>';

foreach ($array as $field) {
    if ($field->position_x != $currentX) {
        $currentX = $field->position_x;
        $table .= "</tr>";
    }
    $table .= "<td class='field' data-id='$field->field_id'>{$field->draw()}</td>";
}

$table .= "</tr>";
$valueTable = <<<VT
<td id="money">$session->money</td>
<td id="water">$session->water</td>
<td id="seeds">$session->seeds</td>
<td id="harvest">$session->harvest</td>
VT;


?>

<html>
<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">

</head>
<body>
<?php require __DIR__ . "/../views/headers/header.php"; ?>
<table class="table table-bordered">
    <?= $table?>
</table>
<table class="table table-bordered">
    <tr>
        <th>Money</th>
        <th>Water</th>
        <th>Seeds</th>
        <th>Harvest</th>
    </tr>
    <tr>
        <?= $valueTable; ?>
    </tr>
    <tr>
        <td id="cheat">+100 (cheat) =)</td>
        <td id="bWater">Click to Buy water</td>
        <td id="bSeeds">Click to Buy seeds</td>
        <td id="sHarvest">Click to Sell harvest</td>
    </tr>
</table>

<div id="answer"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="js/session.js"></script>
</body>
</html>
