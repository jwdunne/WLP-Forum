<?php
require_once ('fun.php');

if (!isset($_SESSION['user']))
	die("<br /><br />You need to login to view this page");
$user = $_SESSION['user'];

if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
else $view = $user;

if (isset($_POST['text']))
{
	$text = sanitizeString($_POST['text']);

	if ($text != "")
	{
		$pm = substr(sanitizeString($_POST['pm']),0,1);
		$time = time();
		queryMysql("INSERT INTO messages VALUES(NULL,
				   '$user', '$view', '$pm', $time, '$text')");
	}
}

if ($view != "") {
	if ($view == $user) {
		$name1 = "Your";
		$name2 = "Your";
	} else {
		$name1 = "<a href='members.php?view=$view'>$view</a>'s";
		$name2 = "$view's";
	}

	echo "<h3>$name1 Messages</h3>";
	showProfile($view);
	
	echo <<<_END
		<form method='post' action='messages.php?view=$view'>
		Type here to leave a message:<br />
			<textarea name='text' cols='40' rows='3'></textarea><br />
			Public<input type='radio' name='pm' value='0' checked='checked' />
			Private<input type='radio' name='pm' value='1' />
			<input type='submit' value='Post Message' /></form>
_END;

	if (isset($_GET['erase'])) {
		$erase = sanitizeString($_GET['erase']);
		queryMysql("DELETE FROM messages WHERE id=$erase
				    AND recip='$user'");
	}
	
	$query = "SELECT * FROM messages WHERE recip='$view'
			  ORDER BY time DESC";
	$result = queryMysql($query);
	$num = mysql_num_rows($result);
	
	for ($j = 0 ; $j < $num ; ++$j)
	{
		$row = mysql_fetch_row($result);

		if ($row[3] == 0 ||
		    $row[1] == $user ||
		    $row[2] == $user)
		{
			echo date('M jS \'y g:sa:', $row[4]);
			echo " <a href='messages.php?";
			echo "view=$row[1]'>$row[1]</a> ";

			if ($row[3] == 0)
			{
				echo "wrote: &quot;$row[5]&quot; ";
			}
			else
			{
				echo "whispered: <i><font
				color='#006600'>&quot;$row[5]&quot;</font></i> ";
			}

			if ($row[2] == $user)
			{
				echo "[<a href='messages.php?view=$view";
				echo "&erase=$row[0]'>erase</a>]";
			}
			echo "<br>";
		}
	}
}

if (!$num) echo "<li>No messages yet</li><br />";

echo "<br><a href='messages.php?view=$view'>Refresh messages</a>";
echo " | <a href='friends.php?view=$view'>View $name2 friends</a>";
?>
