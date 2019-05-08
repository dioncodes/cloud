<?php

require __DIR__ . '/../vendor/autoload.php';

use drpdev\Cloud\Installer;

$message = null;
$success = false;

if (!empty($_POST)) {
	try {
		Installer::generateConfigFile($_POST['token_length'], $_POST['url'], $_POST['send_notifications'] == 'yes', explode(',', str_replace(' ', '', $_POST['emails'])));
		Installer::generateDbConfigFile($_POST['db_host'], $_POST['db_database'], $_POST['db_user'], $_POST['db_password']);
		if ($_POST['send_notifications'] == 'yes') {
			Installer::generateMailConfigFile($_POST['mail_use_smtp'] == 'yes', $_POST['mail_from'], $_POST['mail_server'], $_POST['mail_port'], $_POST['mail_user'], $_POST['mail_password'], $_POST['mail_smtp_secure']);
		}
		$success = true;
	} catch (Exception $e) {
		$message = 'An error occured: ' . $e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/css/style.css?t=<?= filemtime(__DIR__ . '/css/style.css') ?>" />
</head>

<body>
	<div id="content">
		<div class="setup">
			<h1>Setup</h1>
			<?php if (!is_writable(__DIR__ . '/../core/config')) {
				echo '<p class="error">Unfortunately we don\'t have permissions to generate config files in core/config/. Please create them manually.</p>';
			} ?>
			<?= !empty($message) ? '<p class="error">' . $message . '</p>' : '' ?>
			<?php
				if ($success) {
					echo '<p>The config files have been created.</p>';
					if (@rename(__DIR__ . '/install.php', __DIR__ . '/../install.php')) {
						echo '<p>The installation file has been moved to the parent directory. Please make sure that it cannot be access from outside.</p>';
					} else {
						echo '<p class="error"><b>The installation script could not be moved. Please make sure to delete or move the install.php file!</b></p>';
					}

					if (@mkdir(__DIR__ . '/../upload')) {
						echo '<p>The upload folder has been created. Upload files to this folder, to automatically receive an email (if enabled) with a secure download link.</p>';
					} else {
						echo '<p class="error"><b>The upload folder could not be created. Please create it manually in the root directory.</b></p>';
					}

					echo '<p>Please create a cron job that runs cron/check_files.php every <i>n</i> minutes (depending on this interval, the email notification and public link generation might delay up to <i>n</i> minutes)</p>';
				} else {
			?>
			<form method="POST">
				<h2>General Config</h2>
				<label>Public token length:</label>
				<input class="full-width" type="text" name="token_length" placeholder="16" value="16" required /><br />
				<label>URL</label>
				<input class="full-width" type="text" name="url" placeholder="https://cloud.example.com" required /><br />
				<label>Send email notification(s) after upload is complete:</label>
				<input type="checkbox" name="send_notifications" value="yes" checked />
				<label>Notification Emails:</label>
				<input class="full-width" type="text" name="emails" placeholder="mail@example.com, mail2@example.com" />
				<h2>DB Config</h2>
				<label>Host</label>
				<input class="full-width" type="text" name="db_host" placeholder="127.0.0.1" value="127.0.0.1" required /><br />
				<label>Database</label>
				<input class="full-width" type="text" name="db_database" placeholder="cloud" value="" required /><br />
				<label>User</label>
				<input class="full-width" type="text" name="db_user" placeholder="cloud" value="" required /><br />
				<label>Password</label>
				<input class="full-width" type="text" name="db_password" placeholder="" value="" required /><br />
				<div class="mail-config">
					<h2>Mail Config (if "send notifications" is enabled)</h2>
					<input type="checkbox" name="mail_use_smtp" value="yes" /> connect to external SMTP server
					<label>SMTP Secure</label>
					<select name="mail_smtp_secure">
						<option value="ssl">SSL</option>
						<option value="tls">TLS</option>
					</select>
					<label>Email sender address:</label>
					<input class="full-width" type="text" name="mail_from" placeholder="noreply@example.com" value="" /><br />
					<label>Server (leave blank if you are not using smtp):</label>
					<input class="full-width" type="text" name="mail_server" placeholder="mail.example.com" value="" /><br />
					<label>Port</label>
					<input class="full-width" type="text" name="mail_port" placeholder="465" value="465" /><br />
					<label>User</label>
					<input class="full-width" type="text" name="mail_user" placeholder="noreply@example.com" value="" /><br />
					<label>Password</label>
					<input class="full-width" type="text" name="mail_password" /><br />
				</div>

				<button class="btn fill full-width" style="margin: 20px 0;">Generate config files</button>
			</form>
			<?php } ?>
		</div>
	</div>
</body>

</html>
