<?php
require_once ('fun.php');


if (!isset($_SESSION['user']))
	die("<br /><br />You must be logged in to view this page");
$user = $_SESSION['user'];

if (isset($_REQUEST['view']))
{
	$view = sanitizeString($_GET['view']);
	$user = sanitizeString($_SESSION['user']);
	
	if ($view == $user) $name = "Your";
	else $name = "$view's";
	
	echo "<h3>$name Page</h3>";
	showProfile($view);
	echo "<a href='messages.php?view=$view'>$name Messages</a><br />";
	die("<a href='friends.php?view=$view'>$name Friends</a><br />");
}

if (isset($_GET['add']))
{
	$add = sanitizeString($_GET['add']);
	$query = "SELECT * FROM friends WHERE user='$add'
			  AND friend='$user'";
	
	if (!mysql_num_rows(queryMysql($query)))
	{
		$query = "INSERT INTO friends VALUES ('$add', '$user')";
		queryMysql($query);
	}
}
elseif (isset($_GET['remove']))
{
	$remove = sanitizeString($_GET['remove']);
	$query = "DELETE FROM friends WHERE user='$remove'
			  AND friend='$user'";
	queryMysql($query);
}

$result = queryMysql("SELECT user FROM members ORDER BY user");
$num = mysql_num_rows($result);
echo "<h3>Other Members</h3><ul>";

for ($j = 0 ; $j < $num ; ++$j)
{
	$row = mysql_fetch_row($result);
	if ($row[0] == $user) continue;
	
	echo "<li><a href='members.php?view=$row[0]'>$row[0]</a>";
	$query = "SELECT * FROM friends WHERE user='$row[0]'
			  AND friend='$user'";
	$t1 = mysql_num_rows(queryMysql($query));
	
	$query = "SELECT * FROM friends WHERE user='$user'
			  AND friend='$row[0]'";
	$t2 = mysql_num_rows(queryMysql($query));
	$follow = "Request to be Friends";

	if (($t1 + $t2) > 1)
	{
		echo " &harr; and you are friends";
	}
	elseif ($t1)
	{
		echo " &larr; Request pending";
	}
	elseif ($t2)
	{
		$follow = "Accept";
		echo " &rarr; Wants to be your Friend";
	}
	
	if (!$t1)
	{
		echo " [<a href='members.php?add=".$row[0] . "'>$follow</a>]";
	}
	else
	{
		echo " [<a href='members.php?remove=".$row[0] . "'>Unfriend</a>]";
	}
}
?>
