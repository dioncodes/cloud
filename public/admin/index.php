<?php

session_start();

require __DIR__ . '/../../vendor/autoload.php';

use drpdev\Cloud\UserManager;
use drpdev\Cloud\AdminHelper;

if (!UserManager::loggedIn()) {
	header('Location: ../');
	exit;
}

?>
<!DOCTYPE html>
<html>

<head>
	<title>DRPdev Cloud</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/style.css?t=<?= filemtime(__DIR__ . '/../css/style.css') ?>" />
</head>

<body>
	<div id="content">

		<div class="files">
			<h1>Files</h1>

			<?php
			if ($files = AdminHelper::getAllFiles()) {
				echo '<ul>';
				foreach ($files as $file) {
					echo '<li><a href="../f/' . $file->getPublicToken() . '">' . htmlspecialchars($file->getFileName()) . '</a></li>';
				}
				echo '</ul>';
			} else {
				echo '<p>No files found.</p>';
			}

			if ($pendingFiles = AdminHelper::getPendingFiles()) {
				echo '<h1>Pending files</h1><ul>';
				foreach ($pendingFiles as $pendingFile) {
					echo '<li>' . $pendingFile['file_name'] . '</li>';
				}
				echo '</ul>';
			} else {
				echo '<p>No pending files</p>';
			}
			?>
		</div>
	</div>
	<div id="footer">
		<p>&copy; 2019 drpdev.de - <a href="https://drpdev.de/privacy/">Privacy Information / Imprint</a>
	</div>
</body>

</html>
