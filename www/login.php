<?php
session_start();
require("functions.php");

$ravelous = new Ravelous;

if (isset($_SESSION["logged_in"]) and $_SESSION["logged_in"] == 1) {
  header("Location: /account");
}

if (isset($_POST["password"]) and isset($_POST["email"]) and isset($_POST["g-recaptcha-response"]) and empty($_POST["register_email"])) {
	$result = $ravelous->loginUser($_POST["email"], $_POST["password"], $_POST["g-recaptcha-response"], @$_POST["authenticator_code"]);
	if (is_numeric($result)) {
		$_SESSION["logged_in"] = 1;
		$_SESSION["id"] = $result;
		header("Location: /account");
		die(0);
	} else {
		//do nothing, let js take care of the rest
	}

} elseif (isset($_POST['register_email']) and isset($_POST['register_username']) and isset($_POST['register_password']) and isset($_POST['register_passwordrepeat']) and isset($_POST['g-recaptcha-response']) and empty($_POST["email"])) {
	$result = $ravelous->registerUser($_POST["register_email"], $_POST["register_username"], $_POST["register_password"], $_POST["g-recaptcha-response"]);
} elseif (isset($_GET["confirm"])) {
	if ($_GET["confirm"] == "ok") {
		$result = "ERR_CONFIRM_OK";
	} else {
		$result = "ERR_CONFIRM_KEY_WRONG";
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Login</title>
	<link rel="stylesheet" href="/css/login.css">
	<?php 
    include("inc/head.php");
    ?>
	<script src="https://www.google.com/recaptcha/api.js"></script>
	<script src="/js/login.js"></script>
</head>
<body>
	<div class="container">
	
		<div class="background"></div>
		<div class="access">
			<form method="post" id="loginForm">
				<div class="header">
					<img src="/img/ravelous.png" alt="Ravelous Logo">
					<div class="buttons">
						<button type="button" class="login_button active dimmed smaller">Login</button>
						<button type="button" class="register_button dimmed smaller">Register</button>
						<input type="hidden" name="type" value="Login">
					</div>
				</div>
				<div class="login">				
					<label>E-Mail:</label>
					<input type="text" name="email">
					<label>Password:</label>
					<input type="password" name="password">
					<label>2FA (If enabled)</label>
					<input type="text" name="authenticator_code"></input>
				</div>
				<div class="register">
					<label>E-Mail:</label>
					<input type="text" name="register_email">
					<label>Username:</label>
					<input type="text" name="register_username">
					<label>Password:</label>
					<input type="password" name="register_password">
					<label>Password strength:</label>
					<div id="strength">
						<div></div>
					</div>
					<label>Repeat password:</label>
					<input type="password" name="register_passwordrepeat">				
				</div>
				<div class="footer">
					<div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
					<button id="submitForm" type="button" value="Log in">Log in</button>
				</div>
			</form>
		</div>
	</div>
	<script>
		<?php 
		if (isset($result)) {
			echo "alert('".$result."')";
		}
		?>
	</script>
</body>
</html>
