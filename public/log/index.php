<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" href="/favicon.ico" />
<title><?php print $_GET['t'] ? $_GET['t']." - " : "" ?>markkit.net - mark log</title>
<link rel="stylesheet" type="text/css" href="markkit.css" />
<style type="text/css">
body { 
		font: 0.8125em Verdana, sans-serif; 
		color: #333; 
		background: #FFF;
		margin-left: auto;
		margin-right: auto;
		margin-top: 50px;
		width: 750px }
h1 {
	border-bottom: 1px solid;
	margin-top: 50px;
	text-align: right;
}

hr {
	background-color: lightgrey;
	height: 1px;
	border: 0;
	margin-top: 10px;
	margin-bottom: 30px;
}

a {
	color: grey;
	text-decoration: none;
}
a:hover {
	color: blue;
	text-decoration: underline;
}
#copyright {
	text-align: right;
	margin-top: 100px;
	color: grey;
}
#page_selector {
	text-align: right;
	margin-top: 50px;
	border-top: 1px solid red;
}
#page_number {
		font: 5em Verdana, sans-serif; 
		color: lightgrey;
}
</style>
</head>
<body>
<a href="http://markkit.net" style="position: absolute; top: 0; left: 532">
<img src="../markkit-site.png" border="0"/>
</a>
<?php
	define('MARKS_PER_PAGE', 100);

	define('WARAQ_ROOT', '../..');
	require_once WARAQ_ROOT .'/'. 'ini.php';

	require_once "mark.php";
	require_once "pageselector.php";

	$marksDB = $waraq->getparam('markkit/marks/db');
	$marksDBuser = $waraq->getparam('markkit/marks/db/user');
	$marksDBpassword = $waraq->getparam('markkit/marks/db/password');
	Mark::set_db($marksDB, $marksDBuser, $marksDBpassword);

	$param_string = '';
	$options = NULL;
	if (!empty($_GET['u'])) {
		$options['owner'] = $_GET['u'];
		$param_string .= "&u=". $_GET['u'];
	}
	$markLogUrl = $waraq->getparam('markkit/log')->get_url() ."?$param_string". '&p=';
	$marks_count = Mark::count($options);
	$pageSelector = new PageSelector($markLogUrl, MARKS_PER_PAGE, $marks_count);

	$page_number  = isset($_GET['p']) && $_GET['p'] > 0 ? $_GET['p'] : $pageSelector->getLastPage();
	$lowerLimit = $pageSelector->getFirstItem($page_number);
	$options["limit"] = "$lowerLimit,". MARKS_PER_PAGE;
	$options["order"] = " creationDate asc ";
?>
<?php
	if ($_GET['u']) {
		$submitUrl        = $waraq->get("/archive/save/")->url;
		$bookmarkletID    = $_GET['u'];
?>
<p style="position: absolute; top: 0; left: 32">Post marks here using this bookmarklet → 
<a style="border: solid 1px; border-color: ThreeDHighlight ThreeDShadow ThreeDShadow ThreeDHighlight; background-color: ThreeDFace; color: black; padding: 5px" href="javascript:location.href='<?php echo $submitUrl; ?>?page='+encodeURIComponent(location.href)+'&t='+encodeURIComponent(document.title)+'&i=<?php echo $bookmarkletID; ?>'" title="markkit" onclick="javascript:alert('Drag\'n\'Drop in your browser toolbar'); return false;"><?php print $_GET['t'] ? $_GET['t'] : "markkit" ?></a>
</p>
<?php } // personal bookmarklet ?>
<div id="page_number">
<?php print $_GET['t'] ? $_GET['t']." #$page_number" : "#$page_number"; ?>
</div>
<?php
	if ($_GET['u']) {
		$marks = Mark::select($options);
	}
	else {
		$marks = Mark::select("where number >= $lowerLimit group by text, pageUrl order by creationDate limit ". MARKS_PER_PAGE);
	}
	if (empty($marks)) {
		echo "<p>empty</p>";
	} else {
		$m = end($marks);
		$currentPage = $m->pageUrl;
		$currentDate = date("d.m.Y", $m->creationDate);
		echo "<h1>$currentDate</h1>";
		echo $m->toHTMLanchor();
		while ($m = prev($marks)) {
			if ($currentPage != $m->pageUrl) {
				$currentPage = $m->pageUrl;
			} else {
				$markDate = date("d.m.Y", $m->creationDate);
				if ($currentDate != $markDate) {
					$currentDate = $markDate;
					echo "<h1>$currentDate</h1>";
				} else {
					echo " . ";
				}
			}
			echo $m->toHTMLanchor();
		}
	}

?>
<div id="page_selector">
Page: 
<?php
	$links = $pageSelector->getLinks($page_number);
	foreach ($links as $text => $href) {
		if ($href == '#') {
			echo " <b>$text</b> ";
		} else {
			echo " <a href='$href' >$text</a> ";
		}
	}
?>
</div>
<div id="copyright">copyright 2007 Slim Amamou</div>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2571726-1";
urchinTracker();
</script>
</body>
</html>
