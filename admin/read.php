<? 
error_reporting(0);
function runSQL($rsql) {

	include 'config.php';

	$connect = mysql_connect($hostname,$username,$password) or die ("Error: could not connect to database");
	$db = mysql_select_db($dbname);
	$result = mysql_query($rsql) or die ('Cannot connect to DB'); 
	return $result;
	mysql_close($connect);
}

function countRec($fname,$tname,$where) {

$sql = "SELECT count($fname) FROM $tname $where";

$result = runSQL($sql);
while ($row = mysql_fetch_array($result)) {
return $row[0];
}
}
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];

if (!$sortname) $sortname = 'date';
if (!$sortorder) $sortorder = 'asc';
		if($_POST['query']!=''){
			$where = "WHERE `".$_POST['qtype']."` LIKE '%".$_POST['query']."%' ";
		} else {
			$where ='';
		}
		if($_POST['letter_pressed']!=''){
			$where = "WHERE `".$_POST['qtype']."` LIKE '".$_POST['letter_pressed']."%' ";	
		}
		if($_POST['letter_pressed']=='#'){
			$where = "WHERE `".$_POST['qtype']."` REGEXP '[[:digit:]]' ";
		}
$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 10;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$sql = "select * from `schedule` $where $sort $limit";

//$sql = "SELECT id,iso,name,printable_name,iso3,numcode FROM country $where $sort $limit";

$result = runSQL($sql);

$total = countRec('id','schedule',$where);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "\"page\": $page,\n";
$json .= "\"total\": $total,\n";
$json .= "\"rows\": [";
$rc = false;
while ($row = mysql_fetch_array($result)) {
if ($rc) $json .= ",";
$json .= "\n{";
$json .= "\"id\":\"".$row['id']."\",";

//$row['date'] = str_replace(".", "/", $row['date']);

$json .= "\"cell\":[\"".$row['id']."\",\"".$row['date']."\"";
$json .= ",\"".addslashes($row['name'])."\"";
$json .= ",\"".addslashes($row['lname'])."\"";
$json .= ",\"".addslashes($row['phone'])."\"";
$json .= ",\"".addslashes($row['City'])."\"";
$json .= ",\"".addslashes($row['State'])."\"";
$json .= ",\"".addslashes($row['ZIP'])."\"";
$json .= ",\"".addslashes($row['score'])."\"";
$json .= ",\"".addslashes($row['test_num'])."\"";
$json .= ",\"".addslashes($row['app_date'])."\"]";
$json .= "}";
$rc = true;
}
$json .= "]\n";
$json .= "}";
echo $json;
?>