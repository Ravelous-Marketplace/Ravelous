<?php
header('Content-type: application/json');
session_start();
require("../functions.php");
$ravelous = new RavelousShop();
$products = $ravelous->searchKeyWord('', "homepageshow");
$listings = array();
foreach ($products as $key) {
    $product_encoded = explode(":", $key);
    $product = array();
    for ($j = 0; $j < count($product_encoded); $j++) {
        if ($j == 0 || $j == 3 || $j == 4 || $j == 5) {
            $product[$j] = base64_decode($product_encoded[$j]);
        } else {
            $product[$j] = $product_encoded[$j];
        }
    }
    if (isset($_GET['simple'])) {
        require("../inc/product.php");
        $listings[] = $prodcutCardHtml;
    } else {
        $listings[] = $product;
    }
}     
echo json_encode($listings);
?>