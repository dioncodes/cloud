<?php

namespace drpdev\Cloud;

class Database
{

	private $db;

	private function __construct(\PDO $db) {
		$this->db = $db;
	}

	public static function MySQL($config) {
		$db = new \PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=utf8', $config['user'], $config['password']);
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		return new self($db);
	}

	public function pdo() {
		return $this->db;
	}
}
