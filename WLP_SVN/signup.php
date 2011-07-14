<?php

require_once ('fun.php');

$cookieCount = 0; 
if (isset($_COOKIE['visitcount'])) $cookieCount = $_COOKIE['visitcount']; 
	$cookieCount++; 
		setcookie("visitcount",$cookieCount,time()+60*60*24*180,'/',' http://127.0.0.1:8888/server%20bak/wlp_svn/signup.php');
		
echo "
<title>$appname SIGNUP</title>";
?>
<script>
function checkUser(user)
{
	if (user.value == '')
	{
		document.getElementById('info').innerHTML = ''
		return
	}

	params  = "user=" + user.value
	request = new ajaxRequest()
	request.open("POST", "checkuser.php", true)
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	request.setRequestHeader("Content-length", params.length)
	request.setRequestHeader("Connection", "close")
	
	request.onreadystatechange = function()
	{
		if (this.readyState == 4)
		{
			if (this.status == 200)
			{
				if (this.responseText != null)
				{
					document.getElementById('info').innerHTML =
						this.responseText
				}
				else alert("Ajax error: No data received")
			}
			else alert( "Ajax error: " + this.statusText)
		}
	}
	request.send(params)
}

function ajaxRequest()
{
	try
	{
		var request = new XMLHttpRequest()
	}
	catch(e1)
	{
		try
		{
			request = new ActiveXObject("Msxml2.XMLHTTP")
		}
		catch(e2)
		{
			try
			{
				request = new ActiveXObject("Microsoft.XMLHTTP")
			}
			catch(e3)
			{
				request = false
			}
		}
	}
	return request
}
</script>
<?php

$error = $user = $pass = "";
if (isset($_SESSION['username'])) destroySession();

if (isset($_POST['user']))
{
	$user = sanitizeString($_POST['user']); 
	$pass = $_POST['pass'];
	
	if ($user == "") {
		$uerror = "&larr;&nbsp;You didnt specify a Username.";
	} else if (strlen($user) <= 10) {
		$uerror = "&larr;&nbsp;Username must be greater than 10 characters.";
	} else if (!(ctype_alnum($user))){
		$uerror = "&larr;&nbsp;Username must contain ONLY letters/numbers.";
	} else if ($pass == "") {
		$perror = "&larr;&nbsp;You didnt specify a Password.";
	} else if (strlen($pass) <= 10) {
		$perror = "&larr;&nbsp;password must be greater than ten characters.";
	} else {
		$pass = $_POST['pass'];
		//$pass = md5($_POST['pass']);
		$query = "SELECT * FROM members WHERE user='$user'";
		if (mysql_num_rows(queryMysql($query))) {
			$uerror = "That username already exists<br /><br />";
		} else {
			$query = "INSERT INTO members VALUES('$user', '$pass')";
			queryMysql($query);
			die("<h4>your account has been created</h4>Please <a href='login.php'>Log in</a>.");
		}
		
	}
}
echo <<<_XD_
<h3>Sign Up Form</h3>
<form method='post' action='signup.php'><br />
	USERNAME <input type='text' name='user' value='$user'/>&nbsp;$uerror<span id='info'></span><br />
	PASSWORD <input type='password' name='pass' value='$pass' />&nbsp;$perror
	<br />
<input type='submit' value='Signup' />	
</form>
<span style='position:absolute; bottom:15px;left:0px;'><small><sub><b>You are visiting from: $GETip</b></sub></small></span>
<span style='position:absolute; bottom:0px;left:0px;'><small><sub><b>You have been here $cookieCount times!</b></sub></small></span>
_XD_;
?>