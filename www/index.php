<?php
session_start();
require("functions.php");
$ravelous = new Ravelous();
$ravelous_crypto = new RavelousCrypto();
$ravelous_shop = new RavelousShop();
$ravelous_google_authenticator = new RavelousGoogleAuthenticator();
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
    <title>Ravelous</title>
    <link rel="stylesheet" href="/css/index.css">
    <script src="/js/index.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="/css/index.css">
</head>
<body>
    <?php
    include("inc/header.php");
    ?>
    <main>
        <figure id="shopslider">
        </figure>
        <section class="clear thin">
            <div id="products" class="grid bigger product"></div>
        </section>
    </main>
    <script type="text/javascript" src="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js"></script>
</body>
</html>