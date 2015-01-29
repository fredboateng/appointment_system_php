<? 
error_reporting(0);


// mysql connect settings

include 'config.php';

// Create connection
$conn = new mysqli($hostname, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = rtrim($_REQUEST['name'],",");
$lname = rtrim($_REQUEST['lname'],",");
$phone = rtrim($_REQUEST['phone'],",");
$zip = rtrim($_REQUEST['zip'],",");
$date =  rtrim($_REQUEST['date'],",");
$score =   rtrim($_REQUEST['score'],",");
$update = $_REQUEST['update'];
$row_id = $_REQUEST['row_id'];


$sql_check = "SELECT user_id FROM `user` WHERE /*`name`='$name' && */ `lname`='$lname' && `phone`='$phone'";

$user_id = 0;

if(empty($name) or empty($lname)  or empty($zip)  or empty($date)  or $score < 0 or $phone <= 0 ) {
	echo "{\"err\": 1}";
	exit;
}

if($update == 1) {
	$user_id = $conn->query("select app_user_id from app where app_id = $row_id")->fetch_assoc()["app_user_id"];
	$time_updated = 0;

	if ($conn->query("update user set name='$name', lname='$lname',zip='$zip',phone='$phone' where user_id = $user_id") === FALSE) {
		$merge_id = $conn->query("select user_id from user where lname='$lname' and phone='$phone'")->fetch_assoc()["user_id"];
		if($merge_id > 0) {
			$attempts = 0;
			repeat_update:
			if($conn->query("update app set app_user_id = $merge_id where app_user_id = $user_id") === FALSE) {
				if($attempts < 2) {
					$date = strtotime($date);
					$conn->query("update app set app_date='$date' where app_id = $row_id");
					$attempts += 1;
					goto repeat_update;
				}
				else {
					echo "{\"err\": 3}";exit;
				}
			}
			else {
				$conn->query("delete from user where user_id = $user_id");
				echo "{\"err\": 0}";exit;
			}
		}
		else {
			echo "{\"err\": 1}";exit;
		}
	}
	else {
		$date = strtotime($date);
		if ($conn->query("update app set app_date='$date' where app_id = $row_id") === FALSE) {
			echo "{\"err\": 2}";exit;
		}
		else {
			echo "{\"err\": 0}";exit;
		}
	}

	exit;
}

$result = $conn->query($sql_check);
if ($conn->query($sql_check)->num_rows > 0) {
	$user_id = $conn->query($sql_check)->fetch_assoc()["user_id"];
}
else {
		// Generate new ID
		$new_user_id = rand(0,999999999);
		while($conn->query("SELECT 1 FROM `user` WHERE `user_id`='$new_user_id'")->num_rows > 0) {
			$new_user_id = rand(0,999999999);
		}

		$sql_new_session = "INSERT INTO `portnov_pltest`.`user` (`user_id`, `user_key`, `user_time`, `name`, `lname`, `phone`, `zip`) VALUES ('$new_user_id', '".$_COOKIE['validation']."', CURRENT_TIMESTAMP, '$name', '$lname', '$phone', '$zip')";

		if ($conn->query($sql_new_session) === TRUE) {
			$user_id = $new_user_id;
		}
}

$date = strtotime($date);

$sql_add = "INSERT INTO `portnov_pltest`.`app` (`app_id`, `app_user_id`, `app_date`, `score`) VALUES (NULL, '$user_id', '$date','$score');";

if ($conn->query($sql_add) === TRUE) {
	echo "{\"err\": 0}";
	exit;
}
else {
	echo "{\"err\": 2}";
	exit;
}

$conn->close();

 ?>