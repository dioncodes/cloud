<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

use drpdev\Cloud\UserManager;

if (UserManager::loggedIn()) {
	header('Location: ./admin');
	exit;
}

if (!empty($_POST['username'])) {
	if (UserManager::login($_POST['username'], $_POST['password'])) {
		header('Location: ./admin');
		exit;
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
		<h1>Admin login</h1>
		<?= !empty($_GET['error']) && $_GET['error'] == 'error_downloading_file' ? '<p class="error">An error occured while trying to download this file.</p>' : '' ?>
		<?= !empty($_POST['username']) ? '<p class="error">The login credentials you entered were not correct.</p>' : '' ?>
		<form method="POST">
			<input class="full-width" type="text" name="username" placeholder="Username" /><br />
			<input class="full-width" type="password" name="password" placeholder="Password" /><br />
			<button class="btn fill full-width">Login</a>
		</form>
	</div>
</body>

</html>
