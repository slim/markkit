<?php
	require_once "localresource.php";
	require_once "mark.php";

	class PageCopy extends LocalResource
	{
		static $root, $db;

		var $date;
		var $origin;
		var $endOfHTML = '';
		var $startOfHEAD = '';
		var $endOfHEAD = '';

		function __construct($name, $origin, $root = NULL)
		{

			if (!$root) {
				$root = self::$root;
			}
			$this->date = date("c");
			$this->origin = $origin;
			$c = $root->get($name);
			$this->file = $c->get_file();
			$this->url  = $c->get_url();
		}

		function insert($db)
		{
			$id            = $this->id;
			$creationDate  = date_format(date_create($this->date), 'Y-m-d H:m');
			$pageUrl       = $this->origin;
			$text          = addslashes($this->title);
			$owner         = $this->bookmarklet;
			$bookmarklet   = $this->bookmarklet;

			$tableName = Mark::get_table_name();
			$query = "INSERT into $tableName (id, creationDate, pageUrl, text, owner, bookmarklet) values ('$id', '$creationDate', '$pageUrl', '$text', '$owner', '$bookmarklet');";

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$statement = $db->query($query);

			return $statement;
		}

		static function set_root($root)
		{
			if ($root->url[count($root->url)] != '/') {
				$root->url .= '/';
			}
			self::$root =& $root;
		}

		static function set_db($db, $user = NULL, $password = NULL)
		{
			if ($db instanceof PDO) {
				self::$db =& $db;
			} else {
				if (empty($user)) {
					self::$db = new PDO($db);
				} else {
					self::$db = new PDO($db, $user, $password);
				}
			}
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return self::$db;
		}

		static function sql_select($wildcards = NULL)
		{
			return "select distinct pageUrl from marks $wildcards";
		}
		static function select($wildcards = NULL)
		{
			$q = self::sql_select($wildcards);
			$urls = self::$db->query($q);
			$pages = array();
			foreach ($urls as $u) {
				$origin = $u['pageUrl'];
				$name = str_replace(self::$root->url, '', $origin);
				if ($interrogationMark = strpos($name, '?')) {
					$name = substr($name, 0, $interrogationMark);
				}
				if ($hashMark = strpos($name, '#')) {
					$name = substr($name, 0, $hashMark);
				}
				$p = new PageCopy($name, $origin);
				array_push($pages, $p);
			}

			return $pages;
			
		}

		function update()
		{
			$ch = curl_init();
			$timeout = 15; // set to zero for no timeout
			curl_setopt ($ch, CURLOPT_URL, $this->origin);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt ($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			$file_contents = curl_exec($ch);
			$mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			if (preg_match('/xml/', $mimeType)) {
				$file_contents = htmlentities($file_contents);
			}

			if ($content = $file_contents) {

				if (preg_match("/<head[^>]*>/i", $content)) {
					$content = preg_replace("/<head[^>]*>/i", "\\0". $this->startOfHEAD , $content);
				} else {
					$content = preg_replace("/^/", "<head>". $this->startOfHEAD , $content);
				}
				if (preg_match("/</head>/i", $content)) {
					$content = preg_replace("/<\/head>/i", $this->endOfHEAD . "</head>", $content);
				} else {
					$content = preg_replace("/^/", $this->endOfHEAD . "</head>", $content);
				}	
				if (preg_match("/<\/html>\s*^/i", $content)) {
					$content = preg_replace("/<\/html>/i", $this->endOfHTML . "</html>", $content);
				} else { 
					$content = preg_replace("/$/", $this->endOfHTML . "</html>", $content);
				}
				$copy = fopen($this->file, "w");
				if (! fwrite($copy, $content)) {
					throw new Exception("Err: Ecriture");
				}
			}
			return $ch;
		}

		function add_to_html($s)
		{
			$this->endOfHTML .= $s;
		}

		function add_to_head($s)
		{
			$this->endOfHEAD .= $s;
		}

		function add_to_head_start($s)
		{
			$this->startOfHEAD .= $s;
		}
	}

	function url2fileName($url)
	{
		$fileName = $url;
		$forbidden = array('/',':','?','=','&','+','%');
		$fileName = preg_replace('/^http:\/\//', '', $fileName); //remove http://
		$fileName = preg_replace('/#.*$/', '', $fileName); //remove fragment id
		$fileName = str_replace($forbidden, '_', $fileName);

		return $fileName;
	}
