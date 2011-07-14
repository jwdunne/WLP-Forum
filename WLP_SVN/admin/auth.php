<?php

$admin = FALSE;
$moderator = FALSE;

/*PREPARE A QUERY TO THE DB FOR ALL MARKED ADMIN OR MOD*/
$admin_ls = $query;

if (auth($username) == $admin_ls) {
	$name = $results['name'];
	$email = $results['email'];
	$admin = TRUE;
} elseif (auth($username) == $admin_ls) {
	$name = $results['name'];
	$email = $results['email'];
	$moderator = TRUE;
} else {
	$query = $db->prepare(	'SELECT name, email
							FROM users
							WHERE username = :username');
	$query->execute(array('username' => $clean['username']));
	$name = $result['name'];
	$email = $result['email'];
	$admin = FALSE;
	$moderator = FALSE;
}

function authPage($username, $page) {
	if (!Black_ls($username)){
		if(On_Admin($username)) {
			return TRUE;
		} elseif (allowPage($username, $page)) {
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		return FALSE;
	}
}