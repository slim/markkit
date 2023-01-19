<?php
	require "persistance.php";

	class User implements sql, persistance
	{
		static $db;
		public $id, $password, $email;

		function __construct($id = NULL)
		{
			$this->id = $id;
		}

        public static function get_table_name()
        {
            return "user";
        }

        public function toSQLinsert()
        {
            $table = self::get_table_name();
            $id       = $this->id;
			$password = $this->password;
			$email    = $this->email;
            $query = "insert into $table (id, password, email) values ('$id', '$password', '$email')";

            return $query;
        }

        public static function select($options = NULL, $db = NULL)
        {
            if (!$db) $db =& self::$db;
            $query  = self::sql_select($options);
            $result = $db->query($query);
            $items  = array();
            foreach ($result as $r) {
                $i           = new User($r['id']);
                $i->password = $r['password'];
                $i->email    = $r['email'];
                $items []= $i;
            }
            return $items;
        }

        function load($db = NULL)
        {
            if (!$db) $db =& self::$db;
            $id = $this->id;
            list($item) = self::select("where id='$id'", $db);
			$this->password = $item->password;
			$this->email    = $item->email;

            return $this;
        }

        static function set_db($db, $user = NULL, $password = NULL)
        {
            if ($db instanceof PDO) {
                self::$db =& $db;
            } else {
                if (empty($user)) {
                    self::$db =& new PDO($db);
                } else {
                    self::$db =& new PDO($db, $user, $password);
                }
            }
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return self::$db;
        }

        function delete($db = NULL)
        {
            if (!$db) $db =& self::$db;
            $id = $this->id;
            $table = self::get_table_name();
            $db->exec("delete from $table where id='$id'");

            return $this;
        }


		function save($db = NULL)
        {
            if (!$db) $db =& self::$db;
            $query = $this->toSQLinsert();
            $id = $this->id;
            $table = self::get_table_name();
            try {
                $db->exec($query);
            } catch (PDOException $e) {
                if ($db->errorCode() == "23000") { // l'id existe dans la table
                    $this->load();
                    $this->value++;
                    $this->delete();
                    $query = $this->toSQLinsert();
                    $db->exec($query);
                } else {
                    die($e->getMessage() .' [<b>'.  $query .'</b>]');
                }
            }
            return $this;
        }

        public static function sql_select($options = NULL)
        {
            $table = self::get_table_name();
            $query = "select * from $table $options";
            return $query;
        }

		public static function fromBookmarklet($bookmarklet)
		{
			require "mark.php";
			Mark::set_db(self::$db);

			list($mark) = Mark::select("where bookmarklet='$bookmarklet'");
			$user = new User($mark->owner);

			return $user;
		}

		function session_start()
		{
		}

		function has_right($right=NULL)
		{
		}
	}
