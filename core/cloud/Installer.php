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
}
