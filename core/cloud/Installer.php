<?php

namespace drpdev\Cloud;

class Installer {

	public static function generateConfigFile($tokenLength, $url, $sendNotifications, $emails) {
		$content = "<?php
return [
	'token_length' => " . $tokenLength . ",
	'url' => '" . $url . "',
	'send_notifications' => " . ($sendNotifications ? 'true' : 'false') . ",
	'emails' => [
		";
		foreach ($emails as $email) {
			$content .= "'$email',";
		}
		$content .= "
	]
];
";
		if (!file_put_contents(__DIR__ . '/../config/config.php', $content)) {
			throw new \Exception('file_write_permission_denied');
		}
	}

	public static function generateDbConfigFile($host, $database, $user, $password) {
		$content = "<?php
return [
	'host'		=> '" . $host . "',
	'database'	=> '" . $database . "',
	'user'		=> '" . $user . "',
	'password'	=> '" . str_replace("'", '\\\'', $password) . "'
];
";
		if (!file_put_contents(__DIR__ . '/../config/db-config.php', $content)) {
			throw new \Exception('file_write_permission_denied');
		}
	}

	public static function generateMailConfigFile($useSmtp, $mailFrom, $server, $port, $user, $password, $smtpSecure = 'ssl') {
		$content = "<?php
return [
	'use_smtp'	=> " . ($useSmtp ? 'true' : 'false') . ",
	'smtp_secure'=> '" . $smtpSecure . "',
	'mail_from'	=> '" . $mailFrom . "',
	'server'	=> '" . $server . "',
	'port'		=> " . $port . ",
	'user'		=> '" . $user . "',
	'password'	=> '" . str_replace("'", '\\\'', $password) . "'
];
";
		if (!file_put_contents(__DIR__ . '/../config/mail-config.php', $content)) {
			throw new \Exception('file_write_permission_denied');
		}
	}

	public static function setUpDatabase() {
		try {
			$dbConfig = require __DIR__ . '/../config/db-config.php';
			$db = Database::MySQL($dbConfig);
		} catch (\Exception $e) {
			throw new \Exception('db_connection_failed');
		}

		try {
			$stmt1 = $db->pdo()->prepare("
			CREATE TABLE `files` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`public_token` varchar(64) NOT NULL DEFAULT '',
				`file_name` varchar(200) NOT NULL DEFAULT '',
				`file_size` bigint(32) DEFAULT NULL,
				`upload_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY `public_token` (`public_token`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4");

			$stmt2 = $db->pdo()->prepare( "
			CREATE TABLE `pending_files` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`file_name` varchar(200) NOT NULL DEFAULT '',
				`file_size` bigint(32) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `file_name` (`file_name`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			");

			$stmt3 = $db->pdo()->prepare( "
			CREATE TABLE `users` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`username` varchar(200) NOT NULL,
				`password` varchar(64) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4
			");

			$e1 = $stmt1->execute();
			$e2 = $stmt2->execute();
			$e3 = $stmt3->execute();

			if (!$e1 || !$e2 || !$e3) {
				throw new \Exception('tables_not_created');
			}
		} catch (\Exception $e) {
			throw new \Exception('tables_not_created');
		}
	}

	public static function createUser($username, $password) {
		try {
			$dbConfig = require __DIR__ . '/../config/db-config.php';
			$db = Database::MySQL($dbConfig);
		} catch (\Exception $e) {
			throw new \Exception('db_connection_failed');
		}

		try {
			$stmt = $db->pdo()->prepare('INSERT INTO `users` SET `username`=:username, `password`=:password');
			$stmt->execute([':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 13])]);
		} catch (\Exception $e) {
			throw new \Exception('admin_user_not_created');
		}
	}
}
