<?php

include("../config/db_login_ravelous.php");
if (mysqli_connect_errno()) {
	echo "<script>alert('Failed to connect to MySQL: " . mysqli_connect_error() . "')</script>";
}

$sql="SELECT username FROM Accounts WHERE email='" . $_SESSION['email'] . "'";
$result=mysqli_query($con,$sql);
$row = mysqli_fetch_row($result);
$username = $row[0];
$email = $_SESSION['email'];
$secret = $_SESSION['secret'];

echo '<input type="hidden" value="' . $username . '"></input>';
echo '<input type="hidden" value="' . $email . '"></input>';

?>
