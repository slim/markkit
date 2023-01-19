<?php

	define('WARAQ_ROOT', '../..');
	require_once WARAQ_ROOT .'/'. 'ini.php';

	require_once "mark.php";

	$marksDB = $waraq->getparam('markkit/marks/db');
	$marksDBuser = $waraq->getparam('markkit/marks/db/user');
	$marksDBpassword = $waraq->getparam('markkit/marks/db/password');
	Mark::set_db($marksDB, $marksDBuser, $marksDBpassword);

	if (empty($_GET['page'])) {
		echo <<<EOT
<form method="get" action="http://192.168.0.40/teh/waraq/markkit/marks/">
<input type="text" id="page" name="page"/>
<input type="submit" />
</form>
EOT;
	die;
	}
	$page  = $_GET['page'];
	$options["pageUrl"] = preg_replace('/#.*/', '', $page);
	if (! empty($_GET['user'])) {
		$options['owner'] = $_GET['user'];
	}
	$options['order'] = " creationDate asc ";

	$marks = Mark::select($options);
	echo json_encode($marks);
