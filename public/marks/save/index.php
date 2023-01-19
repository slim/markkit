<?php
	define('WARAQ_ROOT', '../../..');
	require_once WARAQ_ROOT . '/ini.php';

	require_once "mark.php";
	$db = $waraq->getparam("markkit/marks/db");
	$dbUser = $waraq->getparam("markkit/marks/db/user");
	$dbPass = $waraq->getparam("markkit/marks/db/password");
	Mark::set_db($db, $dbUser, $dbPass);

  	$mark = new Mark;
 	$mark->pageUrl       = $_POST['pageUrl'];
 	$mark->text          = $_POST['text'];
 	$mark->owner         = $_POST['owner'];
  	$mark->startNodePath = $_POST['startNodePath'];
 	$mark->startOffset   = $_POST['startOffset'];
 	$mark->endNodePath   = $_POST['endNodePath'];
 	$mark->endOffset     = $_POST['endOffset'];

	$mark->save();
  	echo json_encode($mark);
