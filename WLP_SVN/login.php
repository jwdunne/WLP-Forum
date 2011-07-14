<?php
require_once ('fun.php');

echo "<title>MEMBER LOGIN $appname</title>
		<h3>MEMEBER'S LOGIN</h3>";
$error = $user = $pass = "";

if (isset($_POST['user'])) {
	$user = sanitizeString($_POST['user']);
	$pass = $_POST['pass'];
	
	if ($user == "") {
		$uerror = "&larr;&nbsp;You need to enter a Username.";
	} else if ($pass == "") {
		$perror = "&larr;&nbsp;You did not enter a password!";
	} else {
		//$pass = md5($_POST['pass']);
		$pass = $_POST['pass'];
		$query = "SELECT user,pass FROM members WHERE user='$user' AND pass='$pass'";

		if (mysql_num_rows(queryMysql($query)) == 0) {
			$uerror = "#DEBUG: function : queryMysql(); r:NULL";
		} else {
			$_SESSION['user'] = $user;
			$_SESSION['pass'] = $pass;
				die("
				<div id='upload_frame' style='margin:50px;'>
					<center style='margin:10px;'>
						<a href='http://127.0.0.1:8888/SERVER%20BAK/WLP_SVN/members.php?view=$user' style='color:#990000;'>Click here if you are not redirected</a>
						<meta http-equiv='refresh' content='1;url=http://127.0.0.1:8888/SERVER%20BAK/WLP_SVN/members.php?view=$user' />
						<p> YOU ARE BEING LOGGED IN..STANDBY..</p>
					</center>
				</div>");
		}
	}
}

echo <<<_END
<form method='post' action='login.php'>
Username <input type='text' name='user' value='$user' />&nbsp;$uerror<br />
Password <input type='password' name='pass' value='$pass' />&nbsp; $perror<br />
<input type='submit' value='Login' />
</form>
<span style="position:absolute; bottom:15px;left:0px;"><small><sub><b>You are visiting me from: $GETip</b></sub></small></span>
<span style="position:absolute; bottom:0px;left:0px;"><small><sub><b>You have been here $cookieCount times!</b></sub></small></span>
_END;
?>
