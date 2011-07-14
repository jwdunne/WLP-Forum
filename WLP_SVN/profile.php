<?php
require_once ('fun.php');

if (!isset($_SESSION['user']))
	die("<br /><br />You need to login to view this page");
$user = $_SESSION['user'];

$w = $h = "";
	
echo "<h3>Edit your Profile</h3>";

if (isset($_POST['text']))
{
	$text = sanitizeString($_POST['text']);
	$text = preg_replace('/\s\s+/', ' ', $text);
	
	$query = "SELECT * FROM profiles WHERE user='$user'";
	if (mysql_num_rows(queryMysql($query)))
	{
		queryMysql("UPDATE profiles SET text='$text' where user='$user'");
	}
	else
	{
		$query = "INSERT INTO profiles VALUES('$user', '$text')";
		queryMysql($query);
	}
}
else
{
	$query  = "SELECT * FROM profiles WHERE user='$user'";
	$result = queryMysql($query);
	
	if (mysql_num_rows($result))
	{
		$row  = mysql_fetch_row($result);
		$text = stripslashes($row[1]);
	}
	else $text = "";
}

$text = stripslashes(preg_replace('/\s\s+/', ' ', $text));

if (isset($_FILES['image']['name'])) {
	
	$saveto = "";
	$usr_uploads = dirname(__FILE__)."/upload/$user";

	if (file_exists($usr_uploads)) {
		$saveto = "upload/$user/$user.jpg";
		//echo "file will be saved to $saveto";
	} else {
		@mkdir($usr_uploads);
		if (!mkdir($usr_uploads)){
			//echo "could not create the directory $usr_uploads";
			$saveto = "upload/$user";
		}
		$saveto = $usr_uploads."/$user.jpg";
		//echo "The file did not exist but was created<br />file will be saved to $saveto";
	}
	
	move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
	$typeok = TRUE;
	
	switch($_FILES['image']['type']) {
		case "image/gif": 	$src = imagecreatefromgif($saveto); break;
		//double case -->
		case "image/jpeg":
		case "image/pjpeg":	$src = imagecreatefromjpeg($saveto); break;
		case "image/png":   $src = imagecreatefrompng($saveto); break;
		default:			$typeok = FALSE; break;
	}
	
	if ($typeok) {
	
		list($width,$height)=getimagesize($saveto);
	
		$max=100;
		$tw=$w;
		$th=$h;
		
		if($w>$h&&$max<$w) {
			$th=$max/$w*$h;
			$tw=$max;
		}
		elseif($h>$w&&$max<$h) {
			$tw=$max/$h*$w;
			$th=$max;
		}
		elseif($max<$w) {
			$tw=$th=$max;
		}
		
		$tmp = @imagecreatetruecolor($tw, $th);
		@imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
		@imageconvolution($tmp, array(
							    array(-1, -1, -1),
							    array(-1, 16, -1),
							    array(-1, -1, -1)
						       ), 8, 0);
		@imagejpeg($tmp, $saveto,100);
		@imagedestroy($src);
		@imagedestroy($tmp);

		
	}
}
	
showProfile($user);

echo <<<_END
<form method='post' action='profile.php' enctype='multipart/form-data'>
Upload Profile Picture:<br />
<input type='file' name='image'/><small><sub>Any size, Any image format</sub></small>
<br /><br />Edit your details<br />
<textarea name='text' cols='40' rows='3' wrap='none'></textarea><br />
<input type='submit' value='Update' />
</form>
_END;
?>
