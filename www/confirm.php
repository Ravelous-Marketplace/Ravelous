<?php 
require("functions.php");
$ravelous = new Ravelous();

if (isset($_GET["key"]) and !empty($_GET["key"]) and $_GET["key"] !== "1") {
	$result = $ravelous->confirmAccount($_GET["key"]);
	if ($result == "ERR_CONFIRM_OK") {
		header("Location: /login/?confirm=ok");
	} else {
		header("Location: /login/?confirm=error");
	}
}

?>