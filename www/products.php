<?php
session_start();
require("functions.php");
$ravelous = new Ravelous();
$ravelous_shop = new RavelousShop();
$ravelous_crypto = new RavelousCrypto();

$shop = $_GET['shop'];
$shop = $ravelous_shop->getShopValuesByName($shop);
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
    <title>Products</title>
    <link rel="stylesheet" href="/css/products.css">
</head>
<body>
    <?php
    include("inc/header.php");
    ?>
    
    <main>
        <section>
            <p class="title"><?php echo $shop['shop_title']?></p>
            <p class="subtitle"><?php echo $shop['shop_description']?></p>
        </section>
        <aside>
            <div id="filter">
                <label>Search:</label>
                <input id="search" type="text" placeholder="Search">
                <label>Sort By:</label>
                <div class="radio">
                    <a class="active">Popularity</a>
                    <a>Price - Low to High</a>
                    <a>Price - High to Low</a>
                </div>
                <label>Filter:</label>
                <div class="inputmiddle">
                    <input type="text" id="lo" placeholder="Low Price"><span>to</span><input type="text" id="hi" placeholder="High Price">
                </div>
                <button onclick="reloadproducts()">Go</button>
                <a id="close" class="button dimmed" href="#">Close</a>
            </div>
            <div>
                <a href="#filter" class="button">Filter</a>
            </div>
        </aside>
        <section class="grid bigger product">
        </section>
        <div class="clearix"></div>
    </main>
    <script src="../js/products.js"></script>
    <script>
    var loggedin = false;
    <?php 
    if (isset($result)) {
        echo "alert('".$result."')";
    }
    if (isset($_SESSION["id"]) && $_GET['shop'] == $ravelous->getAccountValues($_SESSION["id"])['username']) {
        $id = $_SESSION["id"];
        if (!is_numeric($id)) {
        } else {
            echo "loggedin = true";
        }
    }
    ?>
    </script>
</body>
</html>