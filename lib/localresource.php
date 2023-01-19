<?php
	class LocalResource
	{
		var $url;
		var $file;

		function __construct($url, $file)
		{
			$this->url = $url;
			$this->file = $file;
		}

		function LocalResource($url, $file)
		{
			$this->__construct($url, $file);
		}

		function get($path)
		{
			$u = absolutize($this->url .'/'. $path);
			$f = absolutize($this->file .'/'. $path);
			$r = new LocalResource($u, $f);
			return $r;
		}

		function get_file()
		{
			return $this->file;
		}

		function get_url()
		{
			return $this->url;
		}
	}

	function absolutize($path)
	{
		//$path = preg_replace('/\./', '/', $path);
		while (preg_match('/[^\/.]+\/\.\./', $path)) {
			$path = preg_replace('/[^\/.]+\/\.\./', '/', $path);
		}
		error_log($path);

		return $path;
	}
?>
