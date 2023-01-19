<?php
	define('WARAQ_ROOT', '../../..');
	require_once WARAQ_ROOT .'/ini.php';

	session_set_cookie_params('3600', '/', $waraq->getparam('markkit/cookie/domain'));
	session_start();

	session_regenerate_id();

	define('URL_MAX_LENGTH', 200);
	require 'pagecopy.php';

	$marksDB = $waraq->getparam('markkit/marks/db');
	$marksDBuser = $waraq->getparam('markkit/marks/db/user');
	$marksDBpassword = $waraq->getparam('markkit/marks/db/password');
	Mark::set_db($marksDB, $marksDBuser, $marksDBpassword);

	$copyRoot         = $waraq->getparam("markkit/archives");
	$overlibUrl       = $waraq->getparam("overlib/js");
	$prototypeUrl     = $waraq->getparam("prototype/js");
	$markkitUrl       = $waraq->getparam("markkit/js");
	$markkitEnableUrl = $waraq->getparam("markkit/enable");
	$markkitHomeUrl   = $waraq->get('/home/')->url;
	$_SESSION['bookmarkletId'] = $_GET['i'];

	if (isset($_GET['referer'])) { 
		$_GET['page'] = $_SERVER['HTTP_REFERER'];
	}
	if (! empty($_GET['page']) ) { 
		$origin = $_GET['page'];
			
		$originBase = preg_replace('/\/[^\/]+$/', '/', $origin);
		@list($origin, $originFragment) = explode('#', $origin);
		$originFragment = '#'. $originFragment;
		$name = substr(url2fileName($origin), 0, URL_MAX_LENGTH) .".html";
		$copy = new PageCopy($name, $origin, $copyRoot);
		$copy->bookmarklet = $_SESSION['bookmarkletId']; 
		$copy->title = $_GET['t'];
		$copy->owner = $_GET['i'];
		$copy->insert(Mark::$db);
		$saveUrl = $waraq->getparam("markkit/marks/save");
		$loadUrl = $waraq->getparam("markkit/marks/load");
		$header = "<base href='". $originBase ."' />";
		$footer = <<<EOT
<style type='text/css'>::selection { background: yellow; color: blue; } ::-moz-selection { background: yellow; color: blue; }</style>
<script src='$overlibUrl'></script>
<script src='$prototypeUrl'></script>
<script src='$markkitUrl'></script>
<a href="$markkitHomeUrl" style="position: fixed; top: 0; left: 700px; z-index: 9999">
<img src="http://markkit.net/markkit.png" border="0"/>
</a>
<script src='$markkitEnableUrl'></script>
EOT;
		if (preg_match('/\.xml$/', $origin) || preg_match('/\.txt$/', $origin) || preg_match('/\.css$/', $origin) || preg_match('/\.js$/', $origin)) {
			$copy->add_to_head_start("<code>");
			$copy->add_to_html("</code>");
		}
		$copy->add_to_head_start($header);
		$copy->add_to_html($footer);
		if (isInRealm($copyRoot->get_url(), $origin) || isInRealm('http://my.ebay.com', $origin) || isInRealm('https://www.facebook.com', $origin) || isInRealm('http://www.hsbc.co.uk', $origin) || isInRealm('https://twitter.com', $origin)) {
			$destination = $origin;
		} else {
			try {
                $ch = $copy->update();
				$mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
				if (preg_match('/xml/', $mimeType)) {
					$copy = new PageCopy($name, $origin, $copyRoot);
					$copy->add_to_head_start("<pre>");
					$copy->add_to_html("</pre>");
					$copy->add_to_html($footer);
                	$ch = $copy->update();
				}
            } catch(Exception $e) {
                $error = "<b>SORRY</b> something went wrong. please retry later. meanwhile, I will keep you informed on <a href='http://markkit.soup.io'>markkit news</a>";
                die("<div style='background-color: yellow; border: 2px solid red; padding: 10px; margin: 10px;'>$error</div>");
            }
			$destination = $copy->get_url();
		}
		$destination .= "?s=". session_id() . $originFragment;
		header("Location:". $destination );
	} else {
		header("HTTP/1.0 400 Bad Request");
	}

function isInRealm($realm, $url)
{
	if (substr($url, 0, strlen($realm)) == $realm) {
		return true;
	} else {
		return false;
	}
}
	
?>
