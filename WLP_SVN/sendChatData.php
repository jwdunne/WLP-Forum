<?php

/**
*
*	@James, I need to modify this to NOT
*	delete entries > 50
*
**/

$name =  $_POST["n"];
$text =  $_POST["c"];

$name = str_replace("\'","'",$name);
$name = str_replace("'","\'",$name);
$text = str_replace("\'","'",$text);
$text = str_replace("'","\'",$text);
$text = str_replace("---"," - - ",$text);
$name = str_replace("---"," - - ",$name);

if (strlen($text) > 500) {
	$text = substr($text,0,500); 
}
$text = preg_replace("/([^\s]{50})/","$1 ",$text);

if (strlen($name) > 30) {
	$name = substr($name, 0,30); 
}

if ($name != '' && $text != '') {
	addData($name,$text); //adds new data to the database
	getID(50); //some database maintenance
}

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

function addData($name,$text) {	
	$sql = "INSERT INTO chat (time,name,text) VALUES (NOW(),'".$name."','".$text."')";
	$conn = getDBConnection();
	$results = mysql_query($sql, $conn);
		if (!$results || empty($results)) {
			echo 'There was an error creating the entry';
			end;
		}
}

function getID($position) {
	$sql = 	"SELECT * FROM chat ORDER BY id DESC LIMIT ".$position.",1";
	$conn = getDBConnection(); 
	$results = mysql_query($sql, $conn);
		if (!$results || empty($results)) {
			echo 'There was an error creating the entry';
			end;
		}
	while ($row = mysql_fetch_array($results)) {
		$id = $row[0];
	}
	if ($id) {
		deleteEntries($id);
	}
}

function deleteEntries($id) {
	$sql = 	"DELETE FROM chat WHERE id < ".$id;
	$conn = getDBConnection();
	$results = mysql_query($sql, $conn);
		if (!$results || empty($results)) {
			echo 'There was an error deletig the entries';
		end;
		}
}
?>