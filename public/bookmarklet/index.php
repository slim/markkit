<?php
	define('WARAQ_ROOT', '../..');
	require_once WARAQ_ROOT .'/ini.php';

  	require "id.php";
	ID::set_seed(file_get_contents(WARAQ_ROOT .'/seed'));

  	$submitUrl        = !empty($_GET['u']) ? $_GET['u'] : $waraq->get("/archive/save/")->url;
  	$bookmarkletImage = !empty($_GET['t']) ? $_GET['t'] : $waraq->get("/markkit-bookmarklet.png")->url;
  	$bookmarkletID    = new ID();
?>
<a href="javascript:location.href='<?php echo $submitUrl; ?>?page='+encodeURIComponent(location.href)+'&t='+encodeURIComponent(document.title)+'&i=<?php echo $bookmarkletID; ?>'" title="markkit" onclick="javascript:alert('Drag\'n\'Drop in your browser toolbar'); return false;"><?php echo "<img src='$bookmarkletImage' alt='markkit' border='0'/>"; ?></a>
