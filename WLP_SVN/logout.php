<?php
require_once ('fun.php');
echo "<title>$appname LOGOUT</title>
		<h3>LOGOUT</h3>";

if (isset($_SESSION['user']))
{
	destroySession();
	echo "YOU HAVE BEEN LOGGED OUT....STANDBY..<br />
	<meta http-equiv='refresh' content='1;url=http://127.0.0.1:8888/server%20bak/WLP_SVN/login.php'>
	";
}
else echo "YOU ARE NOT LOGGED IN!";
?>
