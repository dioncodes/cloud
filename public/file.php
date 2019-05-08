<?php

require __DIR__ . '/../vendor/autoload.php';

$downloader = new drpdev\Cloud\Downloader();

?>
<!DOCTYPE html>
<html>

<head>
	<title>DRPdev Cloud</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/css/style.css?t=<?= filemtime(__DIR__ . '/css/style.css') ?>" />
</head>

<body>
	<div id="content">
		<?php
		try {
			if (!empty($_GET['token']) && $file = $downloader->findFile($_GET['token'])) {
				?>
				<h1><?= htmlspecialchars($file->getFileName()) ?></h1>
				<p>~ <?= $file->getFormattedFileSize() ?></p>
				<div class="btns"><a class="btn fill" href="../download/<?= $file->getPublicToken() ?>">Download file</a></div>
			<?php
			} else {
				throw new \Exception('file_not_found');
			}
		} catch (Exception $e) {
			echo '<h1>Error</h1>
				<p class="error">Unfortunately the requested file was not found or this link has expired.</p>';
		}
		?>
	</div>

	<div id="footer">
		<p>&copy; 2019 drpdev.de - <a href="https://drpdev.de/privacy/">Privacy Information / Imprint</a>
	</div>
</body>

</html>
