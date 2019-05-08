<?php

require __DIR__ . '/../vendor/autoload.php';

$downloader = new drpdev\Cloud\Downloader();

if (empty($_GET['token']) || !$file = $downloader->findFile($_GET['token'])) {
	header('Location: ../');
	exit;
}

try {
	$downloader->downloadFile($file);
} catch (\Exception $e) {
	header('Location: ../?error=error_downloading_file');
}
