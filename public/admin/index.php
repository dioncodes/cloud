<?php

session_start();

require __DIR__ . '/../../vendor/autoload.php';

use drpdev\Cloud\UserManager;
use drpdev\Cloud\AdminHelper;
use drpdev\Cloud\Downloader;

if (!empty($_GET['logout'])) {
	UserManager::logout();
	header('Location: ../');
	exit;
}

if (!UserManager::loggedIn()) {
	header('Location: ../');
	exit;
}

$message = null;

if (!empty($_GET['deleteFile'])) {
	$downloader = new Downloader();
	try {
		if ($file = $downloader->findFile($_GET['deleteFile'])) {
			AdminHelper::deleteFile($file);
		}
	} catch (Exception $e) {
		switch ($e->getMessage()) {
			case 'file_not_found':
				$message = '<p class="error">The file you are trying to delete is not existing anymore.</p>';
				break;

			case 'file_not_deleted':
				$message = '<p class="error">The file was deleted in the database and can not be accessed anymore. The file itself could not be deleted (probably due to lack of permissions). Please delete its folder manually: /files/' . $file->getId() . '</p>';
				break;

			case 'folder_not_deleted':
				$message = '<p class="error">The file was deleted in the database and can not be accessed anymore. The files folder could not be deleted (probably due to lack of permissions). Please delete it manually: /files/' . $file->getId() . '</p>';
				break;

			case 'db_connection_failed':
				$message = '<p class="error">The database connection failed. Please make sure it is configured correctly.</p>';
				break;

			case 'database_entry_not_deleted':
				$message = '<p class="error">The files database row could not be deleted. Please try again.</p>';
				break;

			default:
				$message = '<p class="error">Error while trying to delete file: ' . $e->getMessage() . '</p>';
				break;
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>DRPdev Cloud</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
	<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?t=<?= filemtime(__DIR__ . '/../css/style.css') ?>" />
	<script src="../js/jquery.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script>
		$(function() {
			$('#file-table').DataTable({
				bLengthChange: false,
				pageLength: 10,
				language: {
					search: "_INPUT_",
					searchPlaceholder: "Search",
					paginate: {
						previous: '‹',
						next: '›'
					},
				},
				columns: [
					null,
					null,
					null,
					{
						"orderable": false
					}
				],
				order: [
					[2, "desc"]
				]
			});

			$('.delete-btn').on('click', function(e) {
				if (!confirm('Are you sure to completely delete this file? This action cannot be undone!')) {
					e.preventDefault();
					return false;
				}
			});
		});
	</script>
</head>

<body>
	<div id="content">

		<div class="files">
			<h1>Files</h1>
			<?= $message ?: '' ?>
			<?php
			if ($files = AdminHelper::getAllFiles()) {
				echo '<table id="file-table" width="100%">
				<thead>
					<tr>
						<td>Filename</td>
						<td style="min-width: 100px;" align="right">Filesize</td>
						<td style="min-width: 180px;" align="right">Upload date</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
				';
				foreach ($files as $file) {
					echo '
					<tr>
						<td><a href="../f/' . $file->getPublicToken() . '" target="_blank">' . htmlspecialchars($file->getFileName()) . '</a></td>
						<td align="right" data-order="' . $file->getFileSize() . '">' . $file->getFormattedFileSize() . '</td>
						<td align="right" data-order="' . strtotime($file->getUploadDate()) . '">' . date('d.m.Y H:i', strtotime($file->getUploadDate())) . '</td>
						<td align="right"><a href="?deleteFile=' . $file->getPublicToken() . '" class="delete-btn"><i class="fas fa-trash"></i></a></td>
					</tr>';
				}
				echo '</tbody>
				</table>';
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
		<p>&copy; 2019 drpdev.de - <a href="?logout=1">Logout</a></p>
	</div>
</body>

</html>
