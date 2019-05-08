<?php

namespace drpdev\Cloud;

class AdminHelper {

	public static function getAllFiles() {
		try {
			$dbConfig = require __DIR__ . '/../config/db-config.php';
			$db = Database::MySQL($dbConfig);
		} catch (\Exception $e) {
			throw new \Exception('db_connection_failed');
		}

		$stmt = $db->pdo()->prepare('SELECT * FROM `files` ORDER BY `id` DESC');
		$stmt->execute();

		$files = [];

		if ($result = $stmt->fetchAll()) {
			foreach ($result as $file) {
				$files[] = Downloader::fileFromRow((object) $file);
			}
		}

		return $files;
	}

	public static function getPendingFiles() {
		try {
			$dbConfig = require __DIR__ . '/../config/db-config.php';
			$db = Database::MySQL($dbConfig);
		} catch (\Exception $e) {
			throw new \Exception('db_connection_failed');
		}

		$stmt = $db->pdo()->prepare('SELECT * FROM `pending_files`');
		$stmt->execute();

		return $stmt->fetchAll() ?: [];
	}

}
