<?php
	
	define('WARAQ_ROOT', '..');
	require_once WARAQ_ROOT .'/ini.php';

	require_once "call.php";

	$bookmarkletUrl = $waraq->get('/bookmarklet/')->url;

?>
<html>
<head>
<title>markkit.net - the web highlighter</title>
<!--tipjoy:slim-->
<meta name="description" content="highlight text in any web page" />
<meta name="keywords" content="markkit, highlight, marker, text, color, yellow, words, pen, hi-liter, stabilo, web2.0, bookmarklet, browser, firefox, javascript, html, css, com" />
<link rel="stylesheet" type="text/css" href="markkit.css" />
</head>
<body>
<div id="bookmarklet">
<?php echo call( $bookmarkletUrl ); ?>
<img src="<?php echo $waraq->get('/markkit-arrow.png')->url; ?>" />
</div>
<div id="text">
<h1><span class="marked">HIGHLIGHT</span> text in any web page</h1> 
<p>markkit is a web2.0 text highlighter. <span class="marked">Drag'n'Drop the markkit yellow pen into your browser toolbar.</span> Whenever you want to highlight text in a web page, click on the markkit bookmarklet. markkit works only with <a href="http://www.mozilla.com" >Mozilla Firefox</a>, <a href="http://www.opera.com" >Opera</a>, <a href="http://www.google.com/chrome/" >Google Chrome</a> and <a href="http://www.apple.com/safari/">Apple Safari</a>.</p>
<sup><a href="markkit-demo-video.html" >(watch the 1 minute demo video)</a></sup>
</div>
<hr />
<p><a href="./log/">mark log</a> | <a href="http://markkit.net/archive/search/">search</a> | <a href="mailto:support@markkit.net?subject=markkit">contact</a> | <a href="http://markkit.wordpress.com" >blog</a> | <a class="featured" href="http://getsatisfaction.com/markkit">bugs / support</a> | <a href="http://markkit.net/in-your-site.html">markkit in your blog</a></p>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"> 
</script>
<script type="text/javascript">
_uacct = "UA-2571726-1";
urchinTracker();
</script>
<span id="copyright">copyright 2007 Slim Amamou</span>
</body>
</html>
