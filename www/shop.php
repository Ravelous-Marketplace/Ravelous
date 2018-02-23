<?php
session_start();
require("functions.php");
$ravelous = new Ravelous();
$ravelous_shop = new RavelousShop();
$ravelous_crypto = new RavelousCrypto();

if (isset($_GET['shop'])) {
    $shop = $_GET['shop'];
} else {
    header("Location: /");
    die(0);
}

$shop_name = $shop;
$shop = $ravelous_shop->getShopValuesByName($shop);

if (empty($shop)) {
    header("Location: /");
    die(0);
}

if (isset($_GET["product"]) and isset($_GET["type"])) {
    if ($_SESSION["logged_in"] !== 1) {
        $result = "ERR_NOT_LOGGED_IN";
    } else {
        $seller_id = $shop["realID"];
        $buyer_id = $_SESSION["id"];
        $type = $_GET["type"];

        //get amount
        $amount = $ravelous_shop->getAmountByProduct($_GET["product"], $seller_id);
        if ($amount == "ERR_NO_SUCH_PRODUCT") {
            $result = "ERR_NO_SUCH_PRODUCT";
        } else {
            $result = $ravelous_crypto->transferFunds($seller_id, $buyer_id, $amount, $type);

            //add to sold & brought logs 
            //prevent re-submit
        }

    }
}
$products = $shop["products"];
$products = unserialize($products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php 
    include("inc/head.php");
    ?>
    <title>Shop</title>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css"/>
    <script src="/js/shop.js"></script>
    <link rel="stylesheet" href="/css/shop.css">
</head>
<body>
    <?php
    include("inc/header.php");
    ?>
    <div class="hero" style="background-image: url('data:image/gif;base64,<?php echo $shop['shop_cover_image']; ?>')">
        <div>
            <h1><?php echo $shop['shop_title']; ?></h1>
            <h3><?php echo $shop['shop_description']; ?></h3>
        </div>
    </div>
    <main>
        <section class="dashboard">
            <img src="/img/ravelous_icon.jpg">
            <div id="welcome">
                <p id="user"><?php echo $_GET['shop']?></p>
                <a class="smaller button">Send Message</a>
            </div>
            <div id="information">
                <div id="rating">
                    <p class="num">4.7</p>
                    <p class="desc">AVG Rating</p>
                </div>
                
                <div id="reviewcount">
                    <p class="num">217</p>
                    <p class="desc">Total Reviews</p>
                </div>
                <div id="itemcount">
                    <p class="num">120</p>
                    <p class="desc">Items In Shop</p>
                </div>
                <div id="sold">
                    <p class="num">315</p>
                    <p class="desc">Items Sold</p>
                </div>
            </div>
            <div id="accepted">
                <img src="/img/btc.png">
                <img src="/img/eth.png">
                <img src="/img/ada.jpg">
                <p>Accepted</p>
            </div>
        </section>
        <section class="clear thin">
            <div id="products" class="grid bigger product"></div>
        </section>
        <div class="inline-center">
            <a class="button" href="/products/?shop=<?php echo $_GET['shop']?>">View More</a>
        <div>
    </main>
    </div>
    <style>
    <?php 
    if (isset($_SESSION["id"]) && $_GET['shop'] == $ravelous->getAccountValues($_SESSION["id"])['username']) {
        $id = $_SESSION["id"];
        if (!is_numeric($id)) {
        } else {
            echo ".product .remove { display: block; } .product .title { margin-bottom: 5rem !important; }";
        }
    }
    ?>
    </style>
</body>
</html>