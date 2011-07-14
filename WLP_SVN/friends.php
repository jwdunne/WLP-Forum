<head>
<style type="text/css">
#propic {
	height:150px;
	width:100px;
	border:none;
}
</style>
</head>

<?php
require_once ('fun.php');

if (!isset($_SESSION['user']))
	die("<br /><br /><title>$appname FRIENDS</title><h2>You must be logged in to view this page!</h2>");
$user = $_SESSION['user'];

if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
else $view = $user;

if ($view == $user)
{
	$name1 = "YOUR";
	$name2 = "YOUR";
	$name3 = "YOU ARE";
}
else
{
	$name1 = "<a href='members.php?view=$view'>$view</a>'S";
	$name2 = "$view'S";
	$name3 = "$view IS";
}

echo "
<head>
<style type='text/css'>
#propics {
height:150px;
width:100px;
border:none;
}
</style>
<title>$appname $user's FRIENDS</title>
		<h3>$name1 FRIENDS</h3>";
//showProfile($view); #added so your profile picture isnt showing when you are veiwing friends
$followers = array(); $following = array();

$query  = "SELECT * FROM friends WHERE user='$view'";
$result = queryMysql($query);
$num    = mysql_num_rows($result); 

for ($j = 0 ; $j < $num ; ++$j)
{
	$row = mysql_fetch_row($result);
	$followers[$j] = $row[1];
}

$query  = "SELECT * FROM friends WHERE friend='$view'";
$result = queryMysql($query);
$num    = mysql_num_rows($result);

for ($j = 0 ; $j < $num ; ++$j)
{
	$row = mysql_fetch_row($result);
	$following[$j] = $row[0];
}

$mutual    = array_intersect($followers, $following);
$followers = array_diff($followers, $mutual);
$following = array_diff($following, $mutual);
$friends   = FALSE;

//MUTUAL
if (sizeof($mutual))
{
	echo "<h2><b>$name2 MUTUAL FRIENDS:</b></h2><table><tr><td>
	<style type='text/css'>
	img.#propics {
	border:0px;
	height:150;
	width:100;
	}
	</style>
	";
	foreach($mutual as $friend)
		echo "
		<tr><td><a href='members.php?view=$friend'>$friend</a></td>
		<td><a href='members.php?view=$friend'><img id='propics' title='$friend&#39;s profile picture' src='upload/$friend/$friend.jpg' /></a></td></tr>";
	echo "</table>";
	$friends = TRUE;
}
//FOLLOWING YOU
if (sizeof($followers))
{
	echo "<h3><b>$name2 FOLLOWERS</b></h3>";
	foreach($followers as $friend)
		echo "
		<tr><td><a href='members.php?view=$friend'>$friend</a></td>
		<td><a href='members.php?view=$friend'><img id='propics' title='$friend&#39;s profile picture' src='upload/$friend/$friend.jpg' height='150px' width='100px' /></a></td></tr><br />";
	echo "</table>";
	$friends = TRUE;
}
//YOU ARE FOLLOWING
if (sizeof($following))
{
	echo "<h2><b>$name3 FOLLOWING</b></h2><table>";
	foreach($following as $friend)
		echo "
		<tr><td><a href='members.php?view=$friend'>$friend</a></td>
		<td><a href='members.php?view=$friend'><img id='propics' title='$friend&#39;s profile picture' src='upload/$friend/$friend.jpg' height='150px' width='100px'/></a></td></tr>";
	echo "</table>";
	$friends = TRUE;
}

if (!$friends) echo "You dont have any friends yet!<br />
Do you want to find some?<br />
<a href='members.php'> Make Friends!</a><br /><br />
";

//echo "<a href='messages.php?view=$view'>View $name2 messages</a>";
?>
