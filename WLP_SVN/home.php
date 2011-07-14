<html>
	<head>
	<?php
			require_once ('fun.php');
			$uploadLocation = "c:\\xdev\\www\\WLP_SVN\\repo\\";
			$me = shell_exec('whoami');
			echo "<title>HOME $appname</title>";
		?>
		<meta name="Description" content="WE LOVE PROGRAMMING SVN" />
	</head>
	<body>
		<div id="upload_frame">
		<div id="caption">UPLOAD FILE TO REPOS</div>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="fileForm" id="fileForm" enctype="multipart/form-data">
				<div>File to upload:</div>
				<center>
					<table>
						<tr>
							<td>
								<input name="upfile" type="file" size="36">
							</td>
						</tr>
						<tr>
							<td align="center">
								<br/>
								<input class="text" type="submit" name="submitBtn" value="Upload">
							</td>
						</tr>
					</table>
				</center>  
			</form>
			<?php if (isset($_POST['submitBtn'])){ ?>
			<div id="caption">RESULTS</div>
			<div id="result">
				<table width="100%">
					<?php
					$target_path = $uploadLocation . basename( $_FILES['upfile']['name']);
					//if ($_FILES["upfile"]["error"] > 0) {
					//	echo "Return Code: " . $_FILES['upfile']['error'] . "<br />";
					//}
					if(move_uploaded_file($_FILES['upfile']['tmp_name'], $target_path)) {
						echo "The file: ".  basename( $_FILES['upfile']['name']);
						echo "Upload: " . $_FILES['upfile']['name'] . "<br />";
						echo "Type: " . $_FILES['upfile']['type'] . "<br />";
						echo "Size: " . ByteSize($_FILES['upfile']['size']) / 1024 . " KB<br />";
						echo "Temp file: " . $_FILES['upfile']['tmp_name'] . "<br />";
						echo "DO NOT REFRESH IT WILL REUPLOAD! PLEASE NAVIGATE TO ANOTHER PAGE!";
					} else{
						echo "There was an error uploading the file, please try again!";
					}

					?>
				</table>
			</div>
		<?php
			}
			$df = disk_free_space("C:");
			$ds = disk_total_space("C:");
			@$df2 = disk_free_space("D:"); 
			@$ds2 = disk_total_space("D:");
			echo "
			<div style='background-color:#D8D8D8; height:1.9em; border-top-left-radius:2em; border-top-right-radius:2em; vertical-align:center; padding: 15px 0px 15px 15px; position:absolute; right:0px; bottom:0px; width:95%; margin:0 0 0 0px; border-top:0px solid grey; border-left:0px solid grey; border-right:0px solid grey;'>
				<span>SERVER SPACE: </span>
				<span class='space' style='border:1px solid grey; padding: 3px 12px 3px 12px;'>" . ByteSize($df)." / ".ByteSize($ds) . "</span>
			
				<span>BACKUP SPACE</span>
				<span class='space' style='border:1px solid grey; padding: 3px 12px 3px 12px;'>" . ByteSize($df2)." / ".ByteSize($ds2) . "</span>
				<span>USER: </span>
				<span class='space' style='border:1px solid grey; padding: 3px 12px 3px 12px;'>" . $me . "</span>
			
			</div>
			";
		?>
		
		
	</body>
</html>