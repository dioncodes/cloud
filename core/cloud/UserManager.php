<?php

namespace drpdev\Cloud;

class UserManager {

	public static function login( $username, $password) {
		try {
			$dbConfig = require __DIR__ . '/../config/db-config.php';
			$db = Database::MySQL($dbConfig);
		} catch (\Exception $e) {
			throw new \Exception('db_connection_failed');
		}

		$stmt = $db->pdo()->prepare('SELECT * FROM users WHERE `username`=:username');
		$stmt->execute([':username' => strtolower($username)]);
		$user = $stmt->fetchObject();

		if (!empty($user) && password_verify($password, $user->password)) {
			$_SESSION['username'] = $user->username;
			return true;
		} else {
			return false;
		}
	}

	public static function loggedIn() {
		return !empty($_SESSION['username']);
	}

	public static function getCurrentUser() {
		if (empty($_SESSION['username'])) {
			throw new \Exception('not_logged_in');
		}

		return self::getUser($_SESSION['username']);
	}

}
