<?php
header('Content-type: application/json');
session_start();
require("../functions.php");
$ravelous = new Ravelous();
$ravelous_crypto = new RavelousCrypto();
$ravelous_shop = new RavelousShop();


if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: /login/");
    die(0);
}

$id = $_SESSION["id"];
if (!is_numeric($id)) {
    header("Location: /login/");
    die(1);
}
$result = array();
if (isset($_GET["removeproduct"])) {
    $result[] = $ravelous_shop -> removeProduct($id, $_GET["removeproduct"]);
}
echo json_encode($result);
?>