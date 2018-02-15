<?php

$name = $product[0];
$price = $product[1];
$img = $product[2];
$categ = $product[4];
$loc = explode(":",$product[5]);
$city = $loc[2];
$sec = $product[6];
$cur = $product[7];

$date = date('Y/m/d H:i:s', $sec);
$prodcutCardHtml = '<div><img src="data:image/gif;base64,' . $img . '"><p class="title">' . $name . '</p><p class="price">' . $price . ' ' . $cur . '</p><p class="location"><i class="material-icons">location_on</i> ' . $city . '</p><p class="date"><i class="material-icons"><i class="material-icons">access_time</i></i> ' . $date . '</p></div>';

?>