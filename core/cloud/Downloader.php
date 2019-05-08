<?php

namespace drpdev\Cloud;

class Downloader {

	private $db;

	public function __construct() {
		$dbConfig = require __DIR__ . '/../config/db-config.php';
		$this->db = Database::MySQL($dbConfig);
	}

	/**
	 * find file for a given public token
	 *
	 * @param string $token
	 * @return object file
	 */
	public function findFile(string $token) {
		$stmt = $this->db->pdo()->prepare('SELECT * FROM `files` WHERE `public_token`=:token');
		$stmt->execute([':token' => $token]);

		if ($stmt->rowCount() == 1 && $result = $stmt->fetchObject()) {
			return self::fileFromRow($result);
		}

		throw new \Exception('file_not_found');
	}

	public function downloadFile($file) {
		$path = __DIR__ . '/../../files/' . $file->getId() . '/' . $file->getFileName();

		if (!file_exists($path)) {
			throw new \Exception('file_not_found');
		}

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $file->getFileName() . '"');
		header('Expires: 0');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($path));
		header('Cache-Control: private, no-transform, no-store, must-revalidate');

		readfile($path);
	}

	/**
	 * creates file object from db result row
	 *
	 * @param object $row
	 * @return File
	 */
	private static function fileFromRow($row) {
		return new File(
			$row->id,
			$row->file_name,
			$row->file_size,
			$row->upload_date,
			$row->public_token
		);
	}
}
