<?php

	define('WARAQ_ROOT', '../..');
	require_once WARAQ_ROOT .'/'. 'ini.php';

	require_once "mark.php";

	$marksDB = $waraq->getparam('markkit/marks/db');
	$marksDBuser = $waraq->getparam('markkit/marks/db/user');
	$marksDBpassword = $waraq->getparam('markkit/marks/db/password');
	Mark::set_db($marksDB, $marksDBuser, $marksDBpassword);

	if (empty($_GET['page'])) {
	die('<h1>Attention!</h1>');
	}
	$page  = $_GET['page'];
	$max_keyword_len = 30;
	$options = " where pageUrl like '$page%' and char_length(text) < $max_keyword_len order by creationDate asc ";

	$marks = Mark::select($options);
	$keywords = htmlspecialchars(join(',', $marks));
	echo "<meta name='keywords' content='$keywords' />";
