<?php
	$tar = "repo/";
	$tar = $tar . basename($_FILES['src']['name']);
	if ($_FILES["src"]["error"] > 0) {
		echo "Return Code: " . $_FILES['src']['error'] . "<br />";
	} elseif (!$_FILES['src']['error']) {
		echo "Upload: " . $_FILES['src']['name'] . "<br />";
		echo "Type: " . $_FILES['src']['type'] . "<br />";
		echo "Size: " . $_FILES['src']['size'] / 1024 . " KB<br />";
		echo "Temp file: " . $_FILES['src']['tmp_name'] . "<br />";
	}	
	if (file_exists("/repo/" . $_FILES['src']['name'])) {
		echo $_FILES['src']['name'] . " already exists. ";
	} else {
		move_uploaded_file($_FILES['src']['tmp_name'],$tar);
	} 
	
?>
