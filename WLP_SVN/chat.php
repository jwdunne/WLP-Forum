<html>
<head>
	<?php
		require_once ('fun.php');
		$lines = file('http://127.0.0.1:8888/server%20bak/WLP_SVN/repo/main.cpp');
	?>
	<script src="s/cs.js" language="JavaScript" type="text/javascript"></script>
<?php
if (!isset($_SESSION['user']))
	die("<br /><br />You must be logged in to view this page!");
$user = $_SESSION['user'];

echo <<<_END
<title>DEMO POSTS - $appname</title>
</head>
<body>
<br /><br /><br />
  <div id="header">
    <form id="chatForm" name="chatForm" onsubmit="return false;" action="">
		<input type="hidden" name="name" id="name" value="$user"/>
		<label for="chatbarText">$appname</label><hr />
      	<input type="text" size="55" maxlength="500" name="chatbarText" id="chatbarText" onblur="checkStatus('');" onfocus="checkStatus('active');" />
      	<input onclick="sendComment();" type="submit" id="submit" name="submit" value="submit" />
    </form>
  </div>
<div id="content">
	<div id="side_nav">
	
    </div>
    <div id="code_bar">
		<div id='code_frame'>
			<div class="text">
				<ol>
_END;
				foreach($lines as $line_num => $line) {
					if(is_odd($line_num)) {
						echo "<li class='li1'><div class='de1'>" . cpp_highlight($line) . "</div></li>";
					} else {
						echo "<li class='li2'><div class='de2'>" . cpp_highlight($line) . "</div></li>";
					}
				}
echo <<<_END
				</ol>
			</div>
		</div>
    </div>
    <div id="chatoutput">
		<ul id="outputList">
            <li>
				<div id="user_info">
					<span class="name">XDEV:</span>
				</div>

				<span class="user_comment">
					Welcome to $appname, This will soon be the comments section! for the code on the right!
				</span>
			</li>
		</ul>
	</div>	
</div>
</body>
_END;
?>
</html>