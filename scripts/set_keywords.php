<?php
	define('WARAQ_ROOT', '..');
	require_once WARAQ_ROOT .'/ini.php';

	require "call.php";
	require "pagecopy.php";
	require "mark.php";

	$keywords_url     = $waraq->getparam('markkit/keywords')->url;
	$marksDB          = $waraq->getparam('markkit/marks/db');
	$marksDBuser      = $waraq->getparam('markkit/marks/db/user');
	$marksDBpassword  = $waraq->getparam('markkit/marks/db/password');
	$copyRoot         = $waraq->getparam("markkit/archives");
	PageCopy::set_db($marksDB, $marksDBuser, $marksDBpassword);
	PageCopy::set_root($copyRoot);

	$begin = $argv[1];
	$end   = $argv[2];

	$pages = PageCopy::select("where marks.creationDate between '$begin' and '$end'");
	foreach ($pages as $p) {
		echo $p->origin ."\n";
		$keywords = call($keywords_url ."?page=". $p->origin);
		$p->add_to_head_start($keywords);
		$p->update();
	}
