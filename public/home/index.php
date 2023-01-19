<?php
	define('WARAQ_ROOT', '../..');
	require_once WARAQ_ROOT .'/ini.php';

	session_set_cookie_params('3600', '/', $waraq->getparam('markkit/cookie/domain'));
	session_start();

	if (empty($_SESSION['bookmarkletId'])) {
		header("HTTP/1.0 307 Temporary redirect");
		header("Location: http://markkit.net");
		die;
	}

	$userHomeUrl = $waraq->getparam('markkit/log')->url .'?u='. $_SESSION['bookmarkletId'];
	
	header("HTTP/1.0 307 Temporary redirect");
	header("Location: $userHomeUrl");
