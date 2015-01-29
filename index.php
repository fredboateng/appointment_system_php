<?php 

$wrong_ver = 0;
$wrong_email = 0;
$wrong_phone = 0;
$user_auth = 0;

// mysql connect settings

$servername = "xxxxxxxxxxx";
$username = "xxxxxxxxxxx";
$password = "xxxxxxxxxxx";
$dbname = "xxxxxxxxxxx";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_COOKIE['user_id'])) {
	$sql_check = "SELECT 1 FROM `user` WHERE `user_key`='".$_COOKIE['user_id']."'";

	if ($conn->query($sql_check)->num_rows > 0) {
		$user_auth = 1;
	}
}

if($_REQUEST["action"] == "login" && $user_auth == 0 ) {

	$name = test_input($_POST["name"]);
	$lname = test_input($_POST["lname"]);
	$email = $_POST["email"];
	$phone = test_input($_POST["phone"]);
	$zip = test_input($_POST["zip"]);
	$verif_box = test_input($_POST["verif_box"]);

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  		$wrong_email = 1;
	}

	if(!(md5($verif_box .'figVam') == $_COOKIE['validation'])){
		$wrong_ver = 1;
	}

	if(!(preg_match("/^[0-9]{10}$/", $phone))) {
		$wrong_phone = 1;
	}

	if(!(preg_match("/^[0-9]{5}$/", $zip))) {
		$wrong_zip = 1;
	}

	$sql_check_zip = "select * from zipcodes z where z.ZIP = '$zip'";

	$result = $conn->query($sql_check_zip);
	if ($result->num_rows < 1) {
		$wrong_zip = 1;
	}

	if($name!='' && $lname!=''  && $email!='' && $phone!='' &&  $zip!='' && $wrong_email == 0 && $wrong_phone == 0 && $wrong_ver == 0 && $wrong_zip == 0 ) {

		$sql_check = "SELECT user_id FROM `user` WHERE /* `name`='$name' * && */ `lname`='$lname' && `phone`='$phone'";

		$result = $conn->query($sql_check);
		if ($result->num_rows > 0) {

			if ($conn->query("UPDATE `user` set `user_key` = '". $_COOKIE['validation'] ."' WHERE `user_id` = ".$result->fetch_assoc()["user_id"]."") === TRUE) {
    				setcookie('user_id',$_COOKIE['validation']);
				setcookie('validation','');
				$conn->close();
				header("Location:index.php");
				exit;
			} else {
    				echo "Error: " . $sql . "<br>" . $conn->error;
				$conn->close();
				exit;
			}			
		}
		else {

			// Generate new ID
			$new_user_id = rand(0,999999999);
			while($conn->query("SELECT 1 FROM `user` WHERE `user_id`='$new_user_id'")->num_rows > 0) {
				$new_user_id = rand(0,999999999);
			}

			$sql_new_session = "INSERT INTO `portnov_pltest`.`user` (`user_id`, `user_key`, `user_time`, `name`, `lname`, `phone`, `email`, `zip`) VALUES ('$new_user_id', '".$_COOKIE['validation']."', CURRENT_TIMESTAMP, '$name', '$lname', '$phone', '$email', '$zip')";


			if ($conn->query($sql_new_session) === TRUE) {
    				setcookie('user_id',$_COOKIE['validation']);
				setcookie('validation','');
				$conn->close();
				header("Location:index.php");
				exit;
			} else {
    				echo "Error: " . $sql . "<br>" . $conn->error;
				$conn->close();
				exit;
			}


		}
	}
	setcookie('validation','');
}


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Enrollment test appointment system</title>
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
<script type="text/javascript" src="images/view.js"></script>

</head>
<body id="main_body" >
	<img class="logo" src="http://www.portnov.com/sites/all/themes/portnov/logo.png" alt="Home" />
	<div><a href="/" title="Home">Portnov Computer School</a></div>
        <div>Software QA Training since 1994</div>
	<img id="top" src="images/top.png" alt="">
	<div id="form_container">
<?php

if($user_auth == 1) {
	require 'inner/app.php';
}
else {
	require 'inner/login.php';
}

$conn->close();

 ?>	

		<div id="footer">
			
		</div>
	</div>
	<img id="bottom" src="images/bottom.png" alt="">
	</body>
</html>