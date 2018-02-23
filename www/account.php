<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require("functions.php");
$ravelous = new Ravelous();
$ravelous_crypto = new RavelousCrypto();
$ravelous_shop = new RavelousShop();
$ravelous_google_authenticator = new RavelousGoogleAuthenticator();


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


$data = $ravelous->getAccountValues($id);

//set data's
$username = $data["username"];
$email = $data["email"];
$number = $data["phone_number"];
$currency_settings = explode(":", $data["currency_settings"]);
$accepted_currency = unserialize(base64_decode($currency_settings[0]));
$preffered_currency = base64_decode($currency_settings[1]);
$local_currency = base64_decode($currency_settings[2]);

//set balances
if (!isset($_SESSION["balancesset"])) {
    $balances = $ravelous_crypto->getInternalBalances($id);
    $btc = $balances[0];
    $eth = $balances[1];
    $rave = $balances[2];

    $_SESSION["btc"] = $btc;
    $_SESSION["eth"] = $eth;
    $_SESSION["rave"] = $rave;
    $_SESSION["balancesset"] = "true";
} elseif (isset($_SESSION["balancesset"])) {
    $btc = $_SESSION["btc"];
    $eth = $_SESSION["eth"];
    $rave = $_SESSION["rave"];
}


//set other variables
$rave_eth = $ravelous->rave_eth;
$eth_rave = 1 / $rave_eth;

$shop_values = $ravelous_shop->getShopValues($id);

if (isset($_POST["code"]) and isset($_POST["secret"])) {
    $result = $ravelous_google_authenticator->submitGoogleAuthenticator($_POST["secret"], $_POST["code"],  $id);
} elseif (isset($_POST["shop_title"]) and isset($_POST["shop_description"]) and isset($_FILES["shop_cover_image"])) {
    $result = $ravelous_shop -> addShop($username, $id, $_POST["shop_title"], $_POST["shop_description"], $_FILES["shop_cover_image"]);
} elseif (isset($_POST["product_name"]) and isset($_POST["product_price"]) and isset($_FILES["product_image"]) and isset($_POST["product_description"]) and isset($_POST["product_category"]) and isset($_POST["us2-address"]) and isset($_POST["product_currency"])) {
    $result = $ravelous_shop -> addProduct($id, $_POST["product_name"], $_POST["product_price"], $_FILES["product_image"], $_POST["product_description"], $_POST["product_category"], $_POST["us2-lat"].":".$_POST["us2-long"].":".$_POST["us2-address"], $_POST["product_currency"]);
} elseif (isset($_GET["removeproduct"])) {
    $result = $ravelous_shop -> removeProduct($id, $_GET["removeproduct"]);
} elseif (isset($_POST["shop_title_change"]) or isset($_POST["shop_description_change"]) or isset($_FILES["shop_cover_image_change"])) {
    if (!empty($_POST["shop_title_change"])) {
        $change = "shop_title";
        $data = $_POST["shop_title_change"];
        $result = $ravelous_shop->ChangeShopData($id, $change, $data);
    } 

    if (!empty($_POST["shop_description_change"])) {
        $change = "shop_description";
        $data = $_POST["shop_description_change"];
        $result = $ravelous_shop->ChangeShopData($id, $change, $data);
    }
    if ($_FILES["shop_cover_image_change"]["error"] == 0) {
        $change = "shop_cover_image";
        $data = $_FILES["shop_cover_image_change"];
        $result = $ravelous_shop->ChangeShopData($id, $change, $data);
    }

} elseif (isset($_POST["phone_number"])) {
    $result = $ravelous->addPhoneNumber($id, $_POST["phone_number"]);
} elseif (isset($_POST["current_password"]) &&
    isset($_POST["new_password"]) &&
    isset($_POST["repeat_password"])) {
    if ($_POST["new_password"] != $_POST['repeat_password']) {
        $result = "ERR_NO_MATCH";
    } else {
        $result = $ravelous->changePassword($id, $_POST["current_password"], $_POST["new_password"]);
    }
} elseif (isset($_POST["preffered_currency"])) {
    if (!isset($_POST["accepted_currency"]) || count($_POST["accepted_currency"]) == 0) {
        $result = "ERR_NO_ACCEPTED";
    } else {
        $accepted = [];
        $keys = array_keys($_POST["accepted_currency"]);
        for ($i = 0; $i < count($_POST["accepted_currency"]); $i++) {
            if ($_POST["accepted_currency"][$keys[$i]] == "on") {
                $accepted[] = $keys[$i];
            }
        }
        $result = $ravelous->currencySettings($id, $accepted, $_POST["preffered_currency"], $_POST["local_currency"]);
    }
}



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
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/account.css">
    <script src="/js/account.js"></script>
    <script type="text/javascript" src='https://maps.google.com/maps/api/js?key=AIzaSyBcaAgJ1_uA6MPvUWV7SLJH5jxiCT8NvxM&libraries=places'></script>
    <script src="/js/locationpicker.jquery.js"></script>
</head>
<body>
    <?php
    include("inc/header.php");
    ?>
    <main>
        <section class="dashboard">
            <img src="/img/ravelous_icon.jpg">
            <div id="welcome">
                <h3>Welcome back,</h3>
                <p><?php echo $username; ?></p>
            </div>
            <div id="information">
                <div id="activeListings">
                    <p class="num"><?php echo $ravelous_shop->getProductCount($id) ?></p>
                    <p class="desc">Active listings</p>
                </div>
                <div id="sold">
                    <p class="num">0</p>
                    <p class="desc">Items sold</p>
                </div>
                <div id="minBalance">
                    <p class="num">$0</p>
                    <p class="desc">Min. balance</p>
                </div>
                <div id="estBalance">
                    <p class="num">$0</p>
                    <p class="desc">Est. market value</p>
                </div>
            </div>
        </section>
        <section class="thin nospace">
            <div id="menu">
                <div class="flex">
                    <a href="#profile"><span>Profile</span></a>
                    <a href="#balance"><span>Balance</span></a>
                    <a href="#commerce"><span>Shop Dashboard</span></a>
                    <a href="#deposit"><span>Deposit</span></a>
                    <a href="#withdraw"><span>Withdraw</span></a>
                </div>            
                <div id="more"><a><span>More</span></a></div>
            </div>
        </section>
        <section id="pages">
            <div id="profile">
                <h2>Profile</h2>
                <div class="grid bigger optionbox">
                    <div>
                        <form action="#profile" method="POST">
                            <h6><?php echo $username; ?></h6>
                            <div>
                                <p><?php echo $email; ?></p>
                                <p>Phone: <?php echo $number?></p>
                            </div>
                            <div class="hidden">
                                <label>Change Phone:</label>
                                <input type="text" name="phone_number" value="<?php echo $number?>"/>
                            </div>
                            <div class="button button-moved">Edit</div>
                            <button type="button" orig="Edit">Edit</button>
                            <button class="close hidden dimmed" type="button">Close</button>
                        </form>
                    </div>
                    <div>
                        <form action="#profile" method="POST">
                            <h6>Password</h6>
                            <div>
                                <p>Password: ****</p>
                            </div>
                            <div class="hidden">
                                <p>Change Password</p>
                                <label>Current Password:</label>
                                <input type="password" name="current_password"/>
                                <label>New Password:</label>
                                <input type="password" name="new_password"/>
                                <label>Repeat:</label>
                                <input type="password" name="repeat_password"/>
                            </div>
                            <div class="button button-moved">Edit</div>
                            <button type="button" orig="Edit">Edit</button>
                            <button class="close hidden dimmed" type="button">Close</button>
                        </form>
                    </div>
                    <div>
                        <form action="#profile" method="POST">
                            <h6>2FA</h6>
                            <div>
                                <p>Two Factor Authentication (2FA) not enabled</p>
                            </div>
                            <div class="hidden">
                                <p>To enable, please scan this barcode:</p>
                                <?php
                                $secret = $ravelous_google_authenticator -> generateGoogleAuthenticator($username);
                                ?>
                                <br><label>2FA Code to activate:<label>
                                <input type="text" name="code">
                                <input type="hidden" name="secret" value="<?php echo $secret; ?>">
                            </div>
                            <div class="button button-moved">Edit</div>
                            <button type="button" orig="Enable">Enable</button>
                            <button class="close hidden dimmed" type="button">Close</button>
                        </form>
                    </div>
                    <div>
                        <form action="#profile" method="POST">
                            <h6>Currency</h6>
                            <div>
                                <p>Accepted: <?php echo implode(", ", $accepted_currency)?></p>
                                <p>Preferred: <?php echo $preffered_currency ?></p>
                                <p>Local: <?php echo $local_currency ?></p>
                            </div>
                            <div class="hidden">
                                <label>Accepted:</label>
                                <div class="checkbox">
                                    <input type="checkbox" name="accepted_currency[BTC]" <?php echo $ravelous->checkAccepted($accepted_currency, 'BTC')?>>
                                    <label>Bitcoin (BTC)</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="accepted_currency[ETH]" <?php echo $ravelous->checkAccepted($accepted_currency, 'ETH')?>>
                                    <label>Ethereum (ETH)</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="accepted_currency[RAVE]" <?php echo $ravelous->checkAccepted($accepted_currency, 'RAVE')?>>
                                    <label>RAVE</label>
                                </div><br>
                                <label>Preferred:</label>
                                <select name="preffered_currency">
                                    <option>BTC</option>
                                    <option>RAVE</option>
                                    <option>ETH</option>
                                </select>
                                <label>Local:</label>
                                <select name="local_currency">
                                    <option>USD</option>
                                    <option>EUR</option>
                                </select>
                            </div>
                            <div class="button button-moved">Edit</div>
                            <button type="button" orig="Edit">Edit</button>
                            <button class="close hidden dimmed" type="button">Close</button>
                        </form>
                    </div>
                </div>
            </div>
            <div id="balance">
                <h2>Balance</h2>
                <?php 
                echo "<input type='hidden' name='rave_eth' value=" . $rave_eth . " /><input type='hidden' name='eth_rave' value=" . $eth_rave . "/>";
                echo "<script src='/js/balance.js'></script>";
                echo "<input type='hidden' name='ravelous' value='" . $rave . "' hidden/><input type='hidden' name='ethereum' value='" . $eth . "' hidden/><input type='hidden' name='bitcoin' value='" . $btc . "'/>" 
                ?>
                <p>Current rave value: <?php echo $rave_eth; ?> RAVE / ETH</p>
                <table style="width: 100%;" id="balance-table">
                    <tbody>
                        <tr>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>
                                <select name="alt-currency">
                                    <option value="rave">RAVE</option>
                                    <option value="eth">ETH</option>
                                    <option value="btc">BTC</option>
                                </select>
                                <span>worth</span>
                            </th>
                            <th>
                                <select name="fiat">
                                    <option value="eur">EUR</option>
                                    <option value="usd">USD</option>
                                </select>
                                <span>worth</span>
                            </th>
                        </tr>
                        <tr>
                            <td attr-name="ravelous">RAVE</td>
                            <td><?php echo $rave;?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td attr-name="ethereum">ETH</td>
                            <td><?php echo $eth ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td attr-name="bitcoin">BTC</td>
                            <td><?php echo $btc ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="commerce">
                <h2>Shop Dashboard</h2>
                <?php 
                if ($ravelous_shop->doesUserHaveShop($id) !== false) {
                ?>
                <div class="grid bigger optionbox">
                    <div>
                        <form action="#profile" method="POST" enctype="multipart/form-data">
                            <h6>Your shop</h6>
                            <div>
                                <p><?php echo $shop_values["shop_title"]; ?></p>
                                <p><?php echo $shop_values["shop_description"]; ?></p>
                                <p>Cover Image Enabled</p>
                            </div>
                            <div class="hidden">
                                <label>Change Shop Title:</label>
                                <input type="text" name="shop_title_change" value="<?php echo $shop_values["shop_title"]; ?>"/>
                                <label>Change Shop description:</label><input type="text" name="shop_description_change" value="<?php echo $shop_values["shop_description"]; ?>"/>
                                <label>Change Cover Picture:</label><br>
                                <label class="button" for="picture" id="uploadfilelabel">Upload file</label>
                                <input type="file" name="shop_cover_image_change" id="picture" style="display: none;"/><br>
                            </div>
                            <div class="button button-moved">Edit</div>
                            <button type="button" orig="Edit">Edit</button>
                            <button class="close hidden dimmed" type="button">Close</button>
                        </form>
                    </div>
                    <div>
                        <form action="#commerce" method="POST" enctype="multipart/form-data">
                            <h6>Products</h6>
                            <div>
                                <a href="/products/?shop=<?php echo $username; ?>"><?php echo $ravelous_shop->getProductCount($id) ?> Active Listings</a>
                                <p>0 Views Today</p>
                            </div>
                            <div class="hidden">
                                <p>Add a product to your shop!</p>
                                <label>Product name</label>
                                <input type="text" name="product_name">
                                <label>Product description</label>
                                <input type="text" name="product_description">
                                <label>Location</label>
                                <input type="text" name="us2-address" id="us2-address"/>
                                <div id="us2" style="width: 100%; height: 15rem;"></div><br>
                                <input type="hidden" name="us2-lat" id="us2-lat"/>
                                <input type="hidden" name="us2-long" id="us2-lon"/>
                                <label>Product category</label>
                                <input type="text" name="product_category">
                                <label>Product price</label>
                                <input type="text" name="product_price">
                                <select name="product_currency">
                                    <option value="RAVE">RAVE</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="BTC">BTC</option>
                                    <option value="ETH">ETH</option>
                                </select>
                                <label>Product image</label><br>
                                <label class="button" for="picture2" id="uploadfilelabel">Upload file</label>
                                <input type="file" name="product_image" id="picture2" style="display: none;"/><br>
                                <script>
                                $('#us2').locationpicker({
                                    enableAutocomplete: true,
                                        enableReverseGeocode: true,
                                      radius: 0,
                                      inputBinding: {
                                        latitudeInput: $('#us2-lat'),
                                        longitudeInput: $('#us2-lon'),
                                        radiusInput: $('#us2-radius'),
                                        locationNameInput: $('#us2-address')
                                      },
                                      onchanged: function (currentLocation, radius, isMarkerDropped) {
                                        var addressComponents = $(this).locationpicker('map').location.addressComponents;
                                        updateControls(addressComponents); //Data
                                        }
                                    });
                                    
                                    function updateControls(addressComponents) {
                                      console.log(addressComponents);
                                    }
                                </script>
                            </div>
                            <div class="button button-moved">Add New Product</div>
                            <button type="button" orig="Add New Product">Add New Product</button>
                            <button class="close hidden dimmed" type="button">Close</button>
                        </form>
                    </div>
                    <div>
                        <form action="#withdraw" method="POST" enctype="multipart/form-data">
                            <h6>Balance</h6>
                            <div>
                                <p>Min Balance: $0</p>
                                <p>Est. Market Value: $0</p>
                            </div>
                            <div class="button button-moved">Withdraw</div>
                            <button class="submit" type="button" orig="Withdraw">Withdraw</button>
                        </form>
                    </div>
                </div>
                <?php } else { ?>
                <form action="#profile" class="optionbox" method="POST" enctype="multipart/form-data">
                    <h3>Create your shop!</h3>
                    <div>
                        <p>You have not created a shop yet. To begin creating, press create.</p>
                    </div>
                    <div class="hidden">
                        <label>Shop Title:</label>
                        <input type="text" name="shop_title">
                        <label>Shop Description:</label>
                        <input type="text" name="shop_description">
                        <label>Cover Picture:</label>
                        <label class="button" for="picture" id="uploadfilelabel">Upload file</label>
                        <input type="file" name="shop_cover_image" id="picture" style="display: none;"/><br><br>
                    </div>
                    <div class="button button-moved">Create</div>
                    <button type="button" orig="Create">Create</button>
                    <button class="close hidden dimmed" type="button">Close</button>
                </form>
                <?php } ?>
            </div>
            <div id="deposit">
                <h2>Deposit</h2>
                <p>Not Available</p>
                <?php // $ravelous_crypto -> displayCryptos($id); ?>
            </div>
            <div id="withdraw">
                <h2>Withdraw</h2>
                <p>Not Available</p>
            </div>
        </section>
    </main>
</body>
</html>