<?php
header('Content-type: application/json');
session_start();
require("../functions.php");
$ravelous_shop = new RavelousShop();

if (isset($_GET['i'])) {
    echo json_encode($ravelous_shop->getShops($_GET['i']));
} else {
    echo json_encode($ravelous_shop->getShops(10));
}

?>