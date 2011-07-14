<html>
<head>
<title>View Code</title>
<?php
	require_once ('fun.php');
	$files = rDir("repo");
	$fcount = count($files);
	$dir = "repo/";
	$filename = basename(dirname($_SERVER['PHP_SELF']))."/".$dir;
	@$lines = file('http://127.0.0.1:8888/server%20bak/WLP_SVN/repo/'.$_GET['file']);
	@$gsrc = $_GET['file'];
?>

</head>
<body>
<center>
<?php
	if(isset($gsrc)){
		echo "<h2>Viewing: ".$gsrc.", of the Project : $filename </h2>"; 
	} else {
		echo "<br /><br />";
	}
	
?>
<table style="width:auto; overflow-x:no-content; overflow-y:no-content; margin:10px 0px 5px 5px; border-collapse:collapse;">
<thead style="overflow:hidden;">
	<tr style="background:#fff url('http://127.0.0.1:8888/server%20bak/WLP_SVN/i/head_bg.gif') repeat-x bottom left; color:#E0E0E0; font-size:.9em; font-family:'Lucida Console', Monaco, monospace; font-weight:bold; padding:10px 10px 10px 10px;">
		<th style="border:1px; border-top-left-radius:2em;">NAME</th>
		<th>TYPE</th>
		<th>MODIFIED</th>
		<th>SIZEOF</th>
		<th>VER</th>
		<th style="border-top-right-radius:1em;">&nbsp;</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<td colspan="6" id="dirhead"><center>Number Of Files: <?php echo $fcount;?></center></td>
	</tr>
</tfoot>
<tbody>
	<?php
	foreach ($files as $file) {
		$__VER__ = date("n.j.y", filemtime($dir.$file));
		echo "<tr>
				<td class='dirTD'><a href='dir.php?file=".$file."'>" . strtolower($file) . "</a></td>
				<td class='dirTD' align='center'>." . strtolower(fext($file)) . "</td>
				<td class='dirTD'>" . date(" F d Y H:i:s ", filemtime($dir.$file)) ." </td>
				<td class='dirTD'>" . ByteSize(filesize($dir.$file)) . " </td>
				<!--	<td class='dirTD'><small>" . md5($file) . "</small></td> //-->
				<td class='dirTD' align='center'>" . $__VER__ . "</td>
				<td class='dirTD'><a href='".$file."'><img height='15px' width='15px' src='i/DL.png' /></a>
			</tr>";
	}
	?>
</tbody>
</table>
</center>
<br /><br />
<!-- Current Code to View//-->
		<div id='code_frame'>
			<div class="text">
				<ol>
					<?php					
						if ($gsrc == NULL) {
							echo "	<li class='li1'><div class='de1'>Please select a file from the list to view its source.</div></li>
									<li class='li2'><div class='de2'>Otherwise click the link to download the source.</div></li>";
						} else {
							//loop the selected file ^
							foreach($lines as $line_num => $line) {
								//If the extension for the file is one of cpp use that highlighter
								if (fext($gsrc) == "cpp" || fext($gsrc) == "h") {
									if(is_odd($line_num)) {
										echo "<li class='li1'><div class='de1'>" . cpp_highlight($line) . "</div></li>";
									} else {
										echo "<li class='li2'><div class='de2'>" . cpp_highlight($line) . "</div></li>";
									}
							
								//If the extension for the file is one of xml use that highlighter
								} if (fext($gsrc) == "xml") {
									if(is_odd($line_num)) {
										echo "<li class='li1'><div class='de1'>" . xml_highlight($line) . "</div></li>";
									} else {
										echo "<li class='li2'><div class='de2'>" . xml_highlight($line) . "</div></li>";
									}
								//regular text files
								} if (fext($gsrc) == "txt" || fext($gsrc) == "ini" || fext($gsrc) == "conf") {
									if(is_odd($line_num)) {
										echo "<li class='li1'><div class='de1'>" . $line . "</div></li>";
									} else {
										echo "<li class='li2'><div class='de2'>" . $line . "</div></li>";
									}
								} if(fext($gsrc) == "php" || fext($gsrc) == "php3" || fext($gsrc) == "phtml") {
									php_highlight('./repo/'.$_GET['file']);
									exit;
								}
							}
						}
					?>
				</ol>
			</div>
		</div>
</body>
</html>