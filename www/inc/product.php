<?php

$name = $product[0];
$id = base64_encode($name);
$price = $product[1];
$img = $product[2];
$categ = $product[4];
$loc = explode(":",$product[5]);
$city = $loc[2];
$sec = $product[6];
$cur = isset($product[7]) ? $product[7] : '';

$date = date('Y/m/d H:i:s', $sec);
$prodcutCardHtml = '<div><div class="image" style="background-image: url(data:image/gif;base64,' . $img . ')"></div><p class="category">' . $categ . '</p><p class="title">' . $name . '</p><a class="remove" onclick="removeproduct(\'' . $id . '\')">Remove Product</a><hr><img class="currency" src="/img/eth.png"><p class="price">' . $price . ' ' . $cur . '</p><button class="smaller black buybutton"><i class="material-icons">shopping_basket</i>Buy This</div>';

?>