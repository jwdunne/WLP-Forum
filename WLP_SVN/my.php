<?php

/**
*
*	@NON DEVS: DO NOT TOUCH THESE SETTINGS OR
*	MODIFY THE FUNCTIONS!!! NOTHING WILL WORK
*	THIS IS A CORE FILE!!!!
*
*	-XDEV
*
**/

$dbhost  = '127.0.0.1';
$dbname  = 'wlp_dev';
$dbuser  = 'root';
$dbpass  = '';
$GETip 	 = $_SERVER['REMOTE_ADDR'];
$clean   = array();
$ssql 	 = array();

ignore_user_abort(true);

$cookieCount = 0; 
if (isset($_COOKIE['visitcount']))
	$cookieCount = $_COOKIE['visitcount'];
	$cookieCount++;
	setcookie("visitcount",$cookieCount,time()+60*60*24*30,'/',' 127.0.0.1:8888/', 0, 1);
	//													replace   ^  ^ ^ ^  ^ w/ actual addr..
mysql_connect ( $dbhost, $dbuser, $dbpass ) or die ( mysql_error() );
mysql_select_db ( $dbname ) or die ( mysql_error() );


function createTable($name, $query) {
	if (tableExists($name)) {
		echo "Table '$name' already exists<br />";
	} else {
		queryMysql("CREATE TABLE $name($query)");
		echo "Table '$name' created<br />";
	}
}

function tableExists($name) {
	$result = queryMysql("SHOW TABLES LIKE '$name'");
	return mysql_num_rows($result);
}

function queryMysql($query)	{
	$result = mysql_query($query) or die("#DEBUG: Function: queryMysql; Failed in my.php" . mysql_error());
	return $result;
}

function sanitizeString($var) {
	$var = strip_tags($var);
	$var = htmlentities($var);
	$var = stripslashes($var);
	return mysql_real_escape_string($var);
}

function showProfile($user) {
	if (file_exists("upload/$user/$user.jpg")) 
		echo "<img style='margin-left:25px;' src='upload/$user/$user.jpg' border='0' align='left' />";
		$result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
		if (mysql_num_rows($result)) {
			$row = mysql_fetch_row($result);
			echo stripslashes($row[1]) . "<br clear=left /><br />";
		}
}
?>