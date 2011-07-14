<?php
/**
 * Initial version checking, 
 * this was developed on 5.3.6 
 * so we cant garuntee any thing under 5.3
 *
 **/

if (version_compare(PHP_VERSION, '5.3.0') < 0) {
	die('You are using an unsupported version of PHP. Please upgrade to PHP 5.3.0 or higher');
}

print('<link rel="stylesheet" href="c/default.css" type="text/css" />');

global $__framever__;
$__framever__ = "XDEV FRAMEWORK V1.".date("n.j.y");
session_start();

if (isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
	$loggedin = TRUE;
} else {
	$loggedin = FALSE;
}
/**
 *
 *	TODO: move messages,friends, edit profile to PROFILE
 *		<li><a href='messages.php'><span>MESSAGES</span></a></li>
 *
 *
 **/
 echo "<div id='headcontainer'><div id='headnav'>";
if ($loggedin == TRUE){
	echo "
		<ul>
			<li><a href='members.php?view=$user' class='current'><span>$user:</span><a/></li>
			<li><a id='home' href='home.php'><span>HOME</span></a></li>
			<!-- <li><a href='members.php?view=$user'><span>PROFILE</span></a></li> -->
			<li><a href='friends.php'><span>FRIENDS</span></a></li>
			<li><a href='profile.php'><span>EDIT PROFILE</span></a></li>
			<li><a href='members.php'><span>MEMBERS</span></a></li>
			<li><a id='svn' href='dir.php'><span>PROJECTS</span></a></li>
			<li><a href='chat.php'><span>CHAT</span></a></li>
			<li><a href='logout.php'><span>LOG OUT</span></a></li>
		</ul>";
} else {
	echo "
		<ul>
			<li><a href='index.php'><span>HOME</span></a></li>
			<li><a href='signup.php'><span>SIGN UP</span></a></li>
			<li><a href='login.php'><span>LOGIN</span></a></li>
		</ul>";
}
echo "</div><center><h1>$__framever__</h1></center></div>";
require_once ('my.php');
$appname = $__framever__ . " - WLP DEMO";
$__DEVELOPER__ = '64c6208de21d5993a0a9b5411c7d9951859644fa';
$online = FALSE;
$GETip = $_SERVER['REMOTE_ADDR'].$_SERVER['REMOTE_PORT'];
$perror = '';
$uerror = '';

/**
 *	The next 3 functions are
 *	to create the bread crumbs
 *	fo the repos.
 *
 **/
$makespace = true;
$makeUpper = false;	
$rHome = "REPOS";
$dirDelim = "/";

function dirIndex($dir) {
	$index = '';
	@$dir_handle = opendir($dir);
	if ($dir_handle) {
		while ($file = readdir($dir_handle)) {
			$test = substr(strtolower($file), 0, 6);
			if ($test == 'index.') {
				$index = $file;
				break;
			}
		}
	}
	return $index;
}
function fixArray($array) {
	$clean = array();
	for ($n=0; $n<count($array); $n++) {
		$entry = trim($array[$n]);
		if ($entry != '') $clean[] = $entry;
	}
	return $clean;
}
function nameMod($string) {
	global $makespace;
	global $makeUpper;
	if ($makespace) $string = str_replace('_', ' ', $string);
	if ($makeUpper) $string = ucwords($string);
	return $string;
}
$server = (isset($_SERVER)) ? $_SERVER : $HTTP_SERVER_VARS;
$htmlRoot = (isset($server['DOCUMENT_ROOT'])) ? $server['DOCUMENT_ROOT'] : '';
if ($htmlRoot == '') $htmlRoot = (isset($server['SITE_HTMLROOT'])) ? $server['SITE_HTMLROOT'] : '';
$pagePath = (isset($server['SCRIPT_FILENAME'])) ? $server['SCRIPT_FILENAME'] : '';
if ($pagePath == '') $pagePath = (isset($server['SCRIPT_FILENAME'])) ? $server['SCRIPT_FILENAME'] : '';
$httpPath = ($htmlRoot != '/') ? str_replace($htmlRoot, '', $pagePath) : $pathPath;
$dirArray = explode('/', $httpPath);
if (!is_dir($htmlRoot.$httpPath)) $dirArray = array_slice($dirArray, 0, count($dirArray) - 1);

$linkArray = array();
$thisDir = '';
$baseDir = ($htmlRoot == '') ? '' : $htmlRoot;
for ($n=0; $n<count($dirArray); $n++) {
	$thisDir .= $dirArray[$n].'/';
	$thisIndex = dirIndex($htmlRoot.$thisDir);
	$thisText = ($n == 0) ? $rHome : nameMod($dirArray[$n]);
	$thisLink = ($thisIndex != '') ? '<a href="'.$thisDir.$thisIndex.'">'.$thisText.'</a>' : $thisText;
	if ($thisLink != '') $linkArray[] = $thisLink;
	}

$results = (count($linkArray) > 0) ? implode($dirDelim, $linkArray) : '';
if ($results != '') {
	$breadline = $results;
}
 
 
/**
 *	cache profile pictures so 
 *	there is not a request on the 
 *	server every time we want to see it.
 *
 
function cachePic($pic) {
	if (file_exists("upload/$user/$user.jpg")) {
		$pic = "<img src='upload/$user/$user.jpg' border='0' align='left' />";
		$result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
	}
	if (mysql_num_rows($result)) {
		$row = mysql_fetch_row($result);
		echo stripslashes($row[1]) . "<br clear=left /><br />";
	}
	//ttl is 0 so remember to apc_delete on logout!
	apc_store('propic', $pic, 0);
	apc_fetch('propic');
}

function logout() {
	//apc_delete('propic');
}
**/
function destroySession() {
	$_SESSION=array();
	if (session_id() != "" || isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-2592000, '/');
		session_destroy();
		//logout();
}

/**
*	OLD Badwidth trick ;)
*	
**/
function SaveMe() {
if (@extension_loaded('zlib') && !headers_sent())
	{
		ob_start('ob_gzhandler');
	}
}

/**
*	Turn on if needed.
*
**/
function secCon() {
	if (!($HTTPS == "on")) {
		header("Location: https://$SERVER_NAME$PHP_SELF");
		exit;
	}
}
/**
*	DEV's only.
*
**/
function reqHeaders() {
	
	$headers = apache_request_headers();
	foreach($headers as $header_num => $header) {
		echo "$header_num => $header <br />\n";
	}
}

/**
*	DEV's only, we dont asume our
*	users are developers so why do
*	they want to see an error?
*	thats why I provided the script
*	@ the bottom. when in PROD set to
*	(0);
*
**/
//error_reporting(0);

/**
*	FUNCTION: fext($fn) : [abbr: File Extention($filename)]
*	return is the files extension
*	without the . e.g. fext($file)
*	returns cpp on a .cpp file.
*
**/
function fext($fn) {
	$ext = pathinfo($fn);
	return @$ext['extension'];
}

/**
*	FUNCTION: is_odd($num)
*	return is true?false e.g.
*	is the line_num divisible
*	by 2 if it is then its even
*	else its odd..
*
**/
function is_odd($num) {
	if ($num % 2 == 0) {
		return true;
	}
}
if ($__DEVELOPER__ !== sha1("XDEV")) {
	echo "<br /><br /><h1>THIEF this was made by XDEV!</h1>";
	echo "<br /><h1>IP logged, " . sha1($_SERVER['REMOTE_ADDR'].":".$_SERVER['REMOTE_PORT']) . ", have a nice day!</h1>";
	die;
}

/**
*	FUNCTION: ByteSize($bytes)
*	return is the size of a file
*	other than B(bytes), to do
*	this is simple if $bytes < 1024
*	then the size name is not B
*	and so on for each coresponding
*	to there size measurment.
*
**/
function ByteSize($bytes) { 
	$size = $bytes / 1024; 
	if($size < 1024) { 
		$size = number_format($size, 2); 
		$size .= ' KB'; 
	} else {
		if($size / 1024 < 1024) { 
			$size = number_format($size / 1024, 2); 
			$size .= ' MB'; 
		} else if ($size / 1024 / 1024 < 1024) { 
			$size = number_format($size / 1024 / 1024, 2); 
			$size .= ' GB'; 
		}  
	} 
	return $size; 
}

/**
*	FUNCTION: rDir($dir + $defDir)
*	hacked togeter dir listener..
*	nothing special, still need to
*	figure out how to change the
*	list w/o refresh, when you
*	enter a new folder.
*
*	NOTE: Folder listing is diabled
*	due to a bug.. ?__?
*
**/
function rDir($dir = "./WLP_SVN/repo") { 
$listDir = array(); 

	if($handler = opendir($dir)) { 
		while (($sub = readdir($handler)) !== FALSE) { 
			if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") { 
				if(is_file($dir."/".$sub)) { 
					$listDir[] = $sub; 
				} elseif(is_dir($dir."/".$sub)){ 
					$listDir[$sub] = ReadFolderDirectory($dir."/".$sub); 
				} 
			} 
		} 
	closedir($handler); 
	} 
	return $listDir; 
}


/**
*	SYNTAXER SECTION
*	Below lies all the parsers
*	with there return(s) being
*	($s) Simplicity and keeping
*	everything fairly the same
*	was what i had in mind.
*	To call any one of the parsers
*	simply X_highlight($file)
*	where x is the type of
*	parser you want to call.
*	
*	HTML NOTE: preg_replace("~(&lt;([\w]+)[^&gt;]*&gt;)(.*?)(&lt;\/\\2&gt;)~", $s
**/
function xml_highlight($s) {

	/* KEEP ALL REGULAR TAGS!*/
    $s = htmlspecialchars($s);
    /* < > and </ > tags */
	$s = preg_replace("~&lt;([/]*?)(.*)([\s]*?)&gt;~sU", 			"<font color=\"#0000CC\">&lt;\\1\\2\\3&gt;</font>",$s);
	/* <?xml  ?>  line 2 is <rss>*/
	$s = preg_replace("~&lt;([\?])(.*)([\?])&gt;~sU", 				"<font style=\"background-color:#CCCC00;\" color=\"#CC7700\">&lt;\\1\\2\\3&gt;</font>",$s);
	$s = preg_replace("~&lt;(rss)(.*)&gt;~sU", 						"<font style=\"background-color:#CCCC00;\" color=\"#CC7700\">&lt;\\1\\2&gt;</font>",$s);
	/*OPEN tag name*/
	$s = preg_replace("~&lt;([^\s\?/=])(.*)([\[\s/]|&gt;)~iU", 		"&lt;<font color=\"#CC0000\">\\1\\2</font>\\3",$s);
	/*CLOSE tag name*/
	$s = preg_replace("~&lt;([/])([^\s]*?)([\s\]]*?)&gt;~iU", 		"&lt;\\1<font color=\"#CC00000\">\\2</font>\\3&gt;",$s);
	/*Statement and Arg in the <tag name x="text"> tag */
	$s = preg_replace("~([^\s]*?)\=(&quot;|')(.*)(&quot;|')~isU", 	"<font color=\"#CC00CC\">\\1</font>=<font color=\"#0000CC\">\\2\\3\\4</font>",$s);
	/**
	 * For some reason of another 
	 * nothing is able to "locate"
	 * the CDATA section.. not even phpBB's
	 **/
	//$s = preg_replace("~&lt;(.*)(\!)(.*)(\])&gt;~", 				"&lt;\\1<font color=\"#CC0000\">\\2\\3\\4</font>&gt;",$s);
	$s = preg_replace("~\<\!\[CDATA\[(.*?)\]\]\>~s", 				"", $s);
	/**
	 * The Following is optional
	 * It will remove all the initial whitespace.
	 * Default is off.
	 **/
	//$s = preg_replace('#(?:[\x00-\x1F\x7F]+|(?:\xC2[\x80-\x9F])+)#', '', $s);
	return $s;
}

function cpp_highlight ($s) {

	$comment = '';
	$s = htmlspecialchars($s);
	
	/*	I'm pretty sure this highlights anything after a hash, have not tested much because it works fine.	*/
	$s = preg_replace("~('|#|')(.*)([^\s])~", 			"<font color=\"#990000\">\\1\\2\\3</font>", $s);
	/*	this will highlight anything between the two quotes	*/
	$s = preg_replace("~(&quot;|')(.*?)(&quot;|')~isU", 	"<font color=\"#000099\">\\1\\2\\3</font>",$s);
	/*	mainly for the includes that use <include>	*/
	$s = preg_replace("~(&lt;[A-Za-z0-9_])(.*?)(&gt;|')~i", 		"<font color=\"#000099\">\\1\\2\\3</font>", $s);
	/*	replaces everything with the color BUT if there is some regex's inside it leaves those the same.	*/
	if (preg_match("~//~", $s)) {
		$s = preg_replace("~(\/\/)(.*?)~isU", "<font color=\"#55AA3A\"><b>\\1\\2</b></font>", $s);
	}
	$s = preg_replace("~(\,)~", "<font color=\"#0000CC\"><b>\\1</b></font>", $s);
	$s = preg_replace("~(\()~", "<font color=\"#0000CC\"><b>\\1</b></font>", $s);
	$s = preg_replace("~(\))~", "<font color=\"#0000CC\"><b>\\1</b></font>", $s);
	/*	provided for empty array..	*/
	$s = preg_replace("~(\[\])~", "<font color=\"#0000CC\"><b>\\1</b></font>", $s);
	$s = preg_replace("~(\[)(.*?)(\])~", "<font color=\"#0000CC\"><b>\\1</b><font color=\"#CC0000\">\\2</font><b>\\3</b></font>", $s);
	$s = preg_replace("~(\/\*)(.*?)(\*\/)~", "<font color=\"#55AA3A\">\\1\\2\\3</font>", $s);
	if (preg_match("~(\/\*)~", $s)){
		$s = preg_replace("~(\/\*)(.*?)([\n])~", "<font color=\"#55AA3A\">\\1\\2\\3</font>", $s);
		$comment = true;
	} else if (preg_match("~(\*\/)~", $s)) {
		$s = preg_replace("~(.*?)(\*\/)~", "<font color=\"#55AA3A\">\\1\\2</font>", $s);
		$comments = false;
	}
	
	
	/*	Adding a plus to [^\s] returns odd results..	*/
	//$s = preg_replace("~([\$])(.*)([\s])~", "<font color=\"#0000CC\">\\1\\2\\3</font>", $s);
	
	
	/*****************************************************************
	*
	*	@Operators
	*	!, =, +, -, *, /, %, <, >, <=, >=, +=, -=, *=, /=,
	*	%=, >>=, <<=, &=, ^=, |=, ==, !=, ||, &&, ++, --, ? :,
	*	&, |, ^, ~, <<, >>,
	***
	*
	*	COLORS: Should all be #FF5721 for operator signs.
	*
	***	
	*	
	*	COMMENTS:
	*	techically regex for the doubles (e.g. ++ -- &&) are not needed
	*	because of the single char, but there are here j.i.c.
	*
	*	@Below are escaping everything from the replace text/DOM inside the 
	*	code view <div>'s.
	*
	*	$s = preg_replace("~(\/)~","<font color=\"FF5721\">\\1</font>",$s);
	*														   ^ trigers errors
	*	$s = preg_replace("~(\=)~","<font color=\"FF5721\">\\1</font>",$s);
	*										   ^ triggers errors
	**/
	$s = preg_replace("~(\=\=)~","<font color=\"#FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\+\=)~","<font color=\"#FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\-\=)~","<font color=\"#FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\*\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\/\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\%\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&lt;\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&gt;\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&lt;&lt;=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&gt;&gt;=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&amp;\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\^\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\|\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\!\=)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~([\!])~","<font color=\"#FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\+)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\-)~","<font color=\"FF5721\">\\1</font>",$s);
	//$s = preg_replace("~(\*)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\%)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\|\|)~","<font color=\"FF5721\">||</font>",$s);
	$s = preg_replace("~(&amp;&amp;)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\+\+)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\-\-)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\?)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\:)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&amp;)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\|)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(\^)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("#(\~)#","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&lt;&lt;)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&gt;&gt;)~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&lt;|')~","<font color=\"FF5721\">\\1</font>",$s);
	$s = preg_replace("~(&gt;|')~","<font color=\"FF5721\">\\1</font>",$s);	
	
	/**	keywords	**/
	$s = preg_replace("~(and)([\s])~", 		"<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(asm)([\s])~", 		"<font color=\"#CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(auto)([\s])~", 	"<font color=\"#CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(bit.+and)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(bitor)([\s])~", 	"<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(bool)([\s])~", 	"<font color=\"CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(break)([\s])~", 	"<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(case)([\s])~", 	"<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(catch)([\s])~", 	"<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(char)([\s])~", 	"<font color=\"CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(class)([\s])~", 	"<font color=\"CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(compl)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(const)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(const_cast)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(continue)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(default)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(delete)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(double)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(do)([\s])~", "<font color=\"0000CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(dynamic_cast)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(else)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(enum)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(explicit)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(export)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(extern)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(false)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(float)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(for)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(friend)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(goto)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(if)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(inline)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(int)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(long)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(mutable)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(namespace)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(new)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(not)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(operator)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(private)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(protected)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(public)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(register)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(return)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(reinterpret_cast)([\s])~", "<font color=\"#0000CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(short)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(signed)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(sizeof)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(static)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(static.+_cast)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(string)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(struct)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(switch)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(template)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(this)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(throw)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(true)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(try)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(typedef)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(typeid)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~(typename)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(union)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);
	$s = preg_replace("~(unsigned)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(using)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);
	$s = preg_replace("~(virtual)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(void)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(volatile)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(wchar_t)([\s])~", "<font color=\"#CC00CC\">\\1\\2</font>", $s);	
	$s = preg_replace("~(while)([\s])~", "<font color=\"#0000CC\"><b>\\1\\2</b></font>", $s);	
	$s = preg_replace("~([\s])(or)([\s])~","<font color=\"#0000CC\"><b>\\1\\2\\3<b></font>", $s);
	
	//here after are hacked together and tests!!
	// NOTE: 11JUL11, not needed any more!! but keeping for j.i.c. future needs
	/**
	$s = preg_replace("~un~", "<font color=\"CC0000\">un</font>", $s);
	$s = preg_replace("~_eq~", "<font color=\"CC0000\">_eq</font>", $s);	
	$s = preg_replace("~re.*?erpret_cast~", "<font color=\"CC0000\">reinterpret_cast</font>", $s);
	$s = preg_replace("~_cast~", "<font color=\"CC0000\">_cast</font>", $s);
	$s = preg_replace("~pr.+?f\(~", "<font color=\"CC0000\">printf(</font>", $s);	
	*/
	return $s;
}

/**
*	This is a chepo for now, just used the
*	built in function highlight_file();
*	and then used my foreach teq. and split
*	the file to numbered lines.. once again
*	multiline comments are not working.
*
**/

function php_highlight($s) {
	if(file_exists($s) && is_file($s)) { 
		$code = highlight_file($s, true); 
		$comments = '';
		$arr = explode('<br />', $code); 

		foreach($arr as $line_num => $line) { 
			// single line comment using /* */ 
			if((strstr($line, "/*") !== false) && (strstr($line, '*/') !== false)) {
				$comments = false; 
				$startcolor = "#FF5721";
			
			// multi line comment using /* */ 
			} elseif ((strstr($line, "/*") !== false)) {
				$startcolor = "#FF5721"; 
				$comments = true;
			// @$line = none
			} if (strstr($line, "//") !== false) {
				$startcolor = "#FF5721";
				$comments = true;
			} else {
				$startcolor = "#FF5721"; 
				if($comments) {
					if(strstr($line, "*/") !== false) { 
						$comments = false; 
						$startcolor = "#FF5721"; 
					} else { 
						$comments = true; 
					}   
				//@$line = regex
				} else {
					$comments = false; 
					$startcolor = "green"; 
				}   
			}

			if($comments == true) {
				if (is_odd($line_num)){
					echo "<li class='lio'><div class='divo' style=' color:". $startcolor . ";'>" . $line . "</div></li>"; 
				} else {
					echo "<li class='lie'><div class='dive' style=' color:". $startcolor . ";'>" . $line . "</div></li>";
				}
			} else { 
				if(is_odd($line_num)){
					echo "<li class='lio'><div class='divo' style=' color:". $startcolor . ";'>" . $line . "</div></li>"; 
				} else {
					echo "<li class='lie'><div class='dive' style=' color:". $startcolor . ";'>" . $line . "</div></li>";
				}
			}
		}   
	}
	return $s;
}

?>
<script type="text/javascript">
/**
*
*	XDEV Custom AJAX Error handler
*	This will handle all messages
*	pertaning to JS/HTML if I remember right..
*	if you want to log to the server
*	you must create a logerror.php
*	file and have it $_GET the info.
*	this script is over 2 years old
*	and the error logger was not
*	on my flashdrive..?
*	oh and emailerror.php
*
*
var ERROR_UNDEFINED 	= 0;
var ERROR_NOTICE 		= 1;
var ERROR_WARNING		= 2;
var ERROR_CRITICAL		= 3;
var ERROR_SILENT_WARN	= 4;
var ERROR_SILENT_CRIT	= 5;
var ERROR_SERVER_WARN	= 6;
var ERROR_SERVER_CRIT	= 7;

var XDERROR {
	level: ERROR_UNDEFINED,
	number: -1,
	message: '',
	parameters:null,
	
	sendToUser: function() {
		var format = '';
	
		if (this.parameters.form == this.form_email-occurred)
		else if (this.parameters.form == this.form_email-input)
			fillPopUp('ERROR',format);
	}
	senToUser: funtion(p_method) {
		var param = '';
			param += '<ERROR>';
			param += '<NUMBER>' + this.number + '</NUMBER>';
			param += '<MESSAGE>' + this.message + '</MESSAGE>';
			
			if (this.parameters.file)
				param += '<FILE>' + this.parameters.file + '</FILE>';
			if (this.parameters.trace)
				param += '<TRACE>' + this.parameters.trace + '</TRACE>';
				param += '</ERROR>';
			switch (p_method){
				case 1: ajax.request ('logerror.php', {
					method:'POST', parameters: param
				}); break;
				case 2: ajax.request ('emailerror.php', {
					method:'POST', parameters:param
				});break;
				case 3: ajax.request ('logerror.php', {
					method:'POST', parameters: param
				});break;
				case 4: ajax.request ('emailerror.php', {
					method:'POST', parameters:param
				});break;
			}
	}, throw: function(p_level, p_number, p_message, p_param) {
	this.level 		= p_level;
	this.number 	= p_number & 0xFFFF;
	this.message	= p_message;
	this.parameters = p_param;
	this.parseError();
	}, pareError: function() {
		switch(this.level) {
		case ERROR_NOTICE: this.sendToUser();
			break;
		case ERROR_WARNING:
		case ERROR_SILENT: this.sendToServer(1);
			if(this.level != ERROR_SILENT_WARN)
				this.sendToUser();
			break;
		case ERROR_CRITICAL:
		case ERROR_SILENT_CRIT: this.sendToServer(3);
			if(this.level != ERROR_SILENT_WARN)
				this.sendToUser();
				this.restart(1);
			break;
		}
	} restart: function(p_method) {
		if(p_method && element.visible('popupContainer'))
			setTimeOut('XDERROR.restart(1)', 250);
		else if(!p_method || !element.visible('popupContainer'))
			window.location.href = window.location.href;
	}
}
**/
</script>