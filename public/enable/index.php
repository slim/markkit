<?php
	define('WARAQ_ROOT', '../..');
	require_once WARAQ_ROOT .'/ini.php';

	$saveUrl = $waraq->getparam("markkit/marks/save");
	$loadUrl = $waraq->getparam("markkit/marks/load");
	$userUrl = $archive->get('/user/?PHPSESSID=')->url;

echo <<<EOT

new Ajax.Request('$userUrl'+ getCookie('PHPSESSID'),
	{
		method: 'get',
		onSuccess: MARKKIT.setuser
	});
if ( getCookie('PHPSESSID') == gup('s')) {
	document.body.addEventListener('mouseup', markSelected , false);
}
pageMarkServer = new MarkServer('$saveUrl', '$loadUrl'+encodeURIComponent(document.location.href));
pageMarkServer.loadMarks();

EOT;
?>
if (!gup('s')) {
	document.body.style.marginTop = "50px";
	var message = document.createElement('div');
	message.innerHTML = "<b>Hello! This is not the original page...</b><br />...since somebody kindly highlighted interesting parts using <a href='http://markkit.net' style='color:blue'>markkit the web highligher</a>.";
	message.style.position = "fixed";
	message.style.zIndex = "999";
	message.style.width = "100%";
	message.style.padding = "10px";
	message.style.font = "14px Arial";
	message.style.color = "black";
	message.style.textAlign = "left";
	message.style.top = "0";
	message.style.left = "0";
	message.style.height = "30px";
	message.style.backgroundColor = "yellow";
	message.style.boxShadow = "0 0 8px rgb(0,0,0)";
	message.style.WebkitBoxShadow = "0 0 8px rgb(0,0,0)";
	message.style.MozBoxShadow = "0 0 8px rgb(0,0,0)";
	document.body.appendChild(message);
}
