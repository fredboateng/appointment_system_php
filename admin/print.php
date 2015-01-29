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

$today = strtotime("today");
$tomorrow = strtotime("tomorrow");

$sql = "select name, lname, phone, test_num, app_date from schedule s where s.app_date between $today and $tomorrow";

//$sql = "select name, lname, phone, test_num, app_date  from schedule s where s.app_date between 1422345600 and 2000000000 order by date";

$result = $conn->query($sql);

echo "Date: " . date ( "m/d/Y" , time());

echo '<table width="100%" border="1">';

echo '<tr>';
echo "<td>Time</td>";
echo "<td>Name</td>";
echo "<td>Last name</td>";
echo "<td>Phone #</td>";
echo "<td>ZIP</td>";
echo "<td>Test #</td>";
echo "<td>Score</td>";
echo "<td>End time</td>";
echo '</tr>';

while($row = $result->fetch_assoc()) {
	echo '<tr>';
	echo "<td>" . date ( "h:i" ,$row[app_date]) . "</td>";
	echo "<td>$row[name]</td>";
	echo "<td>$row[lname]</td>";
	echo "<td>$row[phone]</td>";
	echo "<td>$row[zip]</td>";
	echo "<td>$row[test_num]</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo '</tr>';
}

for($i=0;$i<=10;$i++) {
	echo '<tr>';
	echo "<td>&nbsp;</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo '</tr>';
}

echo "</table>";

$conn->close();

?>