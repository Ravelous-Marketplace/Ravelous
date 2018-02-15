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
    <script src="../js/shop.js"></script>
    <link rel="stylesheet" href="/css/shop.css">
</head>
<body>
    <?php
    include("inc/header.php");
    ?>
    <div class="hero" style="background-image: url('data:image/gif;base64,<?php echo $shop['shop_cover_image']; ?>')">
    </div>
    <main>
        <section id="introduction">
            <img src="/img/ravelous_icon.jpg">
            <div id="left_info">
                <p id="title"><?php echo $shop['shop_title']; ?></p><br>
                <p id="user"><?php echo $_GET['shop']?></p>
            </div>
            <div id="right_info">
                <p id="desc"><?php echo $shop['shop_description']; ?></p>
                <div id="supported">
                    <p>Supported Currencies</p>
                    <img src="/img/btc.png">
                    <img src="/img/eth.png">
                    <img src="/img/ada.jpg">
                </div>
            </div>
        </section>
        <section>
            <h3 class="center-text">Featured</h3>
            <div id="featured" class="grid product"></div>
            <h3 class="center-text">Popular</h3>
            <div id="popular" class="grid product"></div>
            <h3 class="center-text">New</h3>
            <div id="new" class="grid product"></div>
        </section>
        <div class="inline-center">
            <a class="button" href="/products/?shop=<?php echo $_GET['shop']?>">View More</a>
        <div>
    </main>
    </div>
</body>
</html>