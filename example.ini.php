<?php
	define('WARAQ_CLASSPATH', realpath(WARAQ_ROOT) . "/lib");
	set_include_path(get_include_path() . PATH_SEPARATOR . WARAQ_CLASSPATH);

	require_once "waraqservice.php";

	$GLOBALS['waraq'] = new WaraqService("http://markkit.net", __DIR__."/public");
	$waraq =& $GLOBALS['waraq'];
	$archive = new LocalResource($waraq->get("untrusted")->url, __DIR__."/public/archive");
	$waraq->setparam("markkit/keywords", $waraq->get("keywords"));
	$waraq->setparam("markkit/enable", $waraq->get("enable")->url);
	$waraq->setparam("markkit/archives", $archive);
	$waraq->setparam("markkit/log", $waraq->get("log/"));
	$waraq->setparam("markkit/marks/save", $waraq->get("marks/save/")->get_url());
	$waraq->setparam("markkit/marks/load", $waraq->get("marks/?page=")->get_url());
	$waraq->setparam("markkit/marks/db", "mysql:host=localhost;dbname=markkit");
	$waraq->setparam("markkit/marks/db/user", "markkit");
	$waraq->setparam("markkit/marks/db/password", "");
	$waraq->setparam("markkit/js", $waraq->url ."/markkit.js");
	$waraq->setparam("overlib/js", $waraq->url ."/overlib421/overlib.js");
	$waraq->setparam("prototype/js", $waraq->url ."/prototype.js");
