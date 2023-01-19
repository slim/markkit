<?php
	define('WARAQ_ROOT', '../..');
	require_once WARAQ_ROOT .'/ini.php';

	session_set_cookie_params('3600', '/', $waraq->getparam('markkit/cookie/domain'));
	session_start();

	echo $_SESSION['bookmarkletId'];
