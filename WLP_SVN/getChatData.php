<?php
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );
header("Content-Type: text/html; charset=utf-8");
$lastID = $_GET['lastID'];

if (!$lastID) {
	$lastID = 0;
}

getData($lastID);

function getDBConnection () {

	include_once ('my.php');
	
	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	if (!$conn) {
			echo "Connection to DB was not possible!";
		end;
		}
		if (!mysql_select_db($dbname, $conn)) {
			echo "No DB with that name seems to exist at the server!";
		end;
		}
		return $conn;
}
// retrieves all messages upto 'id' number 60 all others marked for deletion
function getData($lastID) {
	$sql = 	"SELECT * FROM chat WHERE id > ".$lastID." ORDER BY id ASC LIMIT 60";
	$conn = getDBConnection();
	$results = mysql_query($sql, $conn);
	if (!$results || empty($results)) {
		end;
	}
	while ($row = mysql_fetch_array($results)) {
		$name = $row[2];
		$text = $row[3];
		$id = $row[0];
		if ($name == '') {
			$name = 'no name';
		}
		if ($text == '') {
			$name = 'no message';
		}
		echo $id." ---".$name." ---".$text." ---";
	}
	echo "end";
}
?>