<?php
header('Content-type: application/json');
session_start();
require("../functions.php");
$ravelous_shop = new RavelousShop();

$shop = $_GET['shop'];

$shop_name = $shop;
$shop = $ravelous_shop->getShopValuesByName($shop);

if (empty($shop)) {
    header("Location: /");
    die(0);
}

$products = $shop["products"];
$products = unserialize($products);
$listings = array();

if (!empty($products)) {
    $i = 0;
    foreach ($products as $key) {
        if (isset($_GET['i'])) {
            $maxI = $_GET['i'];
        } else {
            $maxI = 50;
        }
        
        if ($i < $maxI) {
            $product_encoded = explode(":", $key);
            $product = array();
            for ($j = 0; $j < count($product_encoded); $j++) {
                if ($j == 0 || $j == 3 || $j == 4 || $j == 5) {
                    $product[$j] = base64_decode($product_encoded[$j]);
                } else {
                    $product[$j] = $product_encoded[$j];
                }
            }
            $problem = false;
            if (isset($_GET['q'])) {
                if (strpos(strtolower($product[0]), strtolower($_GET['q'])) !== false ||
                    strpos(strtolower($product[3]), strtolower($_GET['q'])) !== false ||
                    strpos(strtolower($product[4]), strtolower($_GET['q'])) !== false ||
                    strpos(strtolower($product[5]), strtolower($_GET['q'])) !== false) {
                } else {
                    $problem = true;
                }
            }
            if (isset($_GET['lo'])) {
                if ($_GET['lo'] > $product[1]) {
                    $problem = true;
                }
            }
            if (isset($_GET['hi'])) {
                if ($_GET['hi'] < $product[1]) {
                    $problem = true;
                }
            }
            if (isset($_GET['categ'])) {
                if (strtolower($_GET['categ']) != strtolower($product[4])) {
                    $problem = true;
                }
            }
            if (isset($_GET['time'])) {
                if ((time() - $product[6]) > ($_GET['time'] * 24 * 60 * 60)) {
                    $problem = true;
                }
            }
            if ($problem == false) {
                if (isset($_GET['simple'])) {
                    require("../inc/product.php");
                    $listings[] = $prodcutCardHtml;
                } else {
                    $listings[] = $product;
                    $i++;
                }
            }
        }
    }
}
echo json_encode($listings);
?>