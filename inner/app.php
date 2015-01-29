<?php if($user_auth == 1) { ?>

<div class="title">Avaliable dates:</div>
<table>
<?php

$day_gap = 30; // Forward day gap
$av_app_time = 2; // Av appointments per time session


$av_time = array(
	"1" => array ("3.00","3.30","4.00","4.30"),
	"3" => array ("3.00","3.30","4.00"),
	"5" => array ("3.00","3.30","4.00","4.30")
);

$holidays = array(
	"2/16",
	"5/25",
	"7/3",
	"7/4",
	"9/7",
	"10/12"
);

function checkTime($time_in,$av_time, $conn,$av_app_time,$holidays) {
	$return = 0;
	if(in_array(getdate($time_in)[wday],array_keys($av_time))) {
		$time =  getdate($time_in)[hours] . "." . sprintf("%02s", getdate($time_in)[minutes]);
		if(in_array($time, $av_time[1]) == 1) {
			$sql_check = "select app_user_id,app_date from app where app_date='$time_in'";
			if($conn->query($sql_check)->num_rows < $av_app_time) {
				$return = 1;
			}
		}
	}


	if(in_array(getdate($time_in)[mon] . "/" . getdate($time_in)[mday],$holidays)) {
		$return = 0;
	}

	if(getdate($time_in)[seconds] > 0) {
		$return = 0;
	}

	if(time() > $time_in) {
		$return = 0;
	}

	return $return;
}

$mysqldate = date( 'Y-m-d H:i:s', time() );
$phpdate = strtotime( $mysqldate );

// Check user app

$have_app = 0;

$check_app_sql = "select FROM_UNIXTIME(app_date, ' %M %D, %Y at %h:%i') as app_date from app where app_user_id = (select user_id from user where user_key='".$_COOKIE['user_id']."') and DATE(FROM_UNIXTIME(app_date)) >= DATE(NOW())";


if($conn->query($check_app_sql)->num_rows > 0) {

	if($_REQUEST["action"] == "cancel") {
		$delete_sql = "delete from app where app_user_id = (select user_id from user where user_key='".$_COOKIE['user_id']."') and FROM_UNIXTIME(app_date) >= NOW()";
		$conn->query($delete_sql);
		echo "<script>window.location='index.php';</script>";
		exit;

	}

	echo "You have an appointment on ";
	echo $conn->query($check_app_sql)->fetch_assoc()["app_date"];
	echo "pm";

	echo "<br>You have an option to <a href=\"#\" onclick=\"if(confirm('Your appointment will be cancelled. Are you sure?') == true) {window.location='index.php?action=cancel'}\">cancel/reschedule</a> it.";

	$have_app = 1;
}
else {
	if($_COOKIE['user_id'] == "0bf235d873af76ba2a0b5f09a6a8bde0") {

		$minimum_month_delay = 3; // months
		$maximum_attempts = 3;
		$sql_check_total_app = "SELECT ap.app_id, ap.app_date
			FROM `app` ap, user u
			WHERE ap.app_user_id = 987915097 AND (UNIX_TIMESTAMP() - ap.app_date) < (2592000 * $minimum_month_delay)
			and u.user_id = ap.app_user_id
			and u.user_key = '".$_COOKIE['user_id']."'  order by ap.app_date";
		
		if($conn->query($sql_check_total_app)->num_rows >= $maximum_attempts) {
			echo "<font color=red>You have reached the maximum number of attempts for an enrollment test.<br>";
			echo "Expected date for registration is after: " ;
			echo date( 'm/d/Y', $conn->query($sql_check_total_app)->fetch_assoc()["app_date"] +  (2592000 * $minimum_month_delay));
			echo "</font>";
			exit;
		}

	}
}

if($_REQUEST["action"] == "appointment" && $have_app == 0) {
	$time_app = $_REQUEST["time"];
	$time_diff = ($time_app - time())/86400;
	if($time_diff > 0 && $time_diff <= $day_gap) {
		if(checkTime($time_app,$av_time, $conn,$av_app_time,$holidays) == 1) {
			$sql_add = "INSERT INTO `portnov_pltest`.`app` (`app_id`, `app_user_id`, `app_date`) VALUES (NULL, (select user_id from user where user_key='".$_COOKIE['user_id']."'), '$time_app');";
			$conn->query($sql_add);
			echo "<script>window.location='index.php';</script>";
			exit;
		}
		else {
			echo "<div id=\"error_message_title\">Sorry, you can't set an appointment for this date. Choose another date and time.</div>";
		}
	}
}

if( $have_app == 0) {

if (getdate(time())[wday] > 0) {
	echo "<tr>";
	for($i=getdate(time())[wday];$i>=0;$i--) {
		echo "<td  valign=\"top\">";
		$day = getdate(time() - 86400 * $i);
		echo "<table class=\"calendarno\"><tr>";
		echo "<td class=\"hilite\">". $day[mon] . "/" . $day[mday] . "/" . $day[year] . "</td></tr>";
		echo "<td class=\"calendarno\">No test</td></tr>";
		echo "</table>";
		echo "</td>";
	}
}



for ($i=1;$i<=$day_gap;$i++) {
	$next_day = getdate(time() + 86400 * $i);

	if($next_day[mday] == 1) {
		if($next_day[wday]  > 0 && $next_day[wday] < 6) {
			echo "</tr><tr><td><b>". $next_day[month] . ", "  .$next_day[year] . "</b></td></tr><tr>";

			for($a=0;$a<$next_day[wday];$a++) {
				echo "<td  valign=\"top\">";
				echo "</td>";
			}
		}
		else {
			echo "<tr><td><b>". $next_day[month] . ", "  .$next_day[year] . "</b></td></tr>";
		}
	}

	if($next_day[wday] == 0) {
		echo "<tr>";
	}

	if(in_array($next_day[wday],array_keys($av_time))) {

		$dayoff = 0;
		$avspaces_cnt = 0;
		$avtimebody = "";


		foreach ($av_time[$next_day[wday]] as &$value) { 
			$genTimeStr = strtotime("$next_day[year]-$next_day[mon]-$next_day[mday] $value");
			if(checkTime($genTimeStr,$av_time, $conn,$av_app_time,$holidays) == 1) {
				$avtimebody .= "<a href=\"index.php?action=appointment&time=$genTimeStr\">$value</a><br>";
				$avspaces_cnt +=1;
			}
		}
		echo "<td  valign=\"top\">";

		if($genTimeStrC < 1422864000) {
			echo "<table class=\"calendarno\"><tr>";
		}
		else if(in_array($next_day[mon] . "/" . $next_day[mday],$holidays)) {
			echo "<table class=\"calendarno\"><tr>";
			$dayoff = 1;
		}
		else if($avspaces_cnt == 0) {
			echo "<table class=\"calendarno\"><tr>";
		}
		else {
			echo "<table class=\"calendar\"><tr>";
		}
		echo "<td class=\"hilite\">" . $next_day[mon] . "/" . $next_day[mday] . "/" . $next_day[year] . " (" . $next_day[weekday] . ")</td></tr>";

		$genTimeStrC = strtotime("$next_day[year]-$next_day[mon]-$next_day[mday]");

		if($genTimeStrC < 1423036800) {
			echo "<td class=\"calendarno\">";
			echo "No test";
		}
		else {

			echo "<td class=\"calendar\">";

			/*foreach ($av_time[$next_day[wday]] as &$value) { 
				$genTimeStr = strtotime("$next_day[year]-$next_day[mon]-$next_day[mday] $value");
				if(checkTime($genTimeStr,$av_time, $conn,$av_app_time,$holidays) == 1) {
					echo "<a href=\"index.php?action=appointment&time=$genTimeStr\">$value</a><br>";
				}
			}*/
			echo $avtimebody;

			if($dayoff == 1) {
				echo "Day off";
			}


		}
		echo "</td></tr>";
		echo "</table>";
		echo "</td>";
	}
	else {
		echo "<td  valign=\"top\">";
		echo "<table class=\"calendarno\"><tr>";
		echo "<td class=\"hilite\">" . $next_day[mon] . "/" . $next_day[mday] . "/" . $next_day[year] . "</td></tr>";
		echo "<td class=\"calendarno\">No test</td></tr>";
		echo "</table>";
		echo "</td>";
	}

	if($next_day[wday] == 6) {
		echo "</tr>";
	}

}

}

?>

</table>

<?php } ?>