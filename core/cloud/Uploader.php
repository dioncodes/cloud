<?php

namespace drpdev\Cloud;

class Uploader {

	private $db;
	private $config;
	private $log;

	public function __construct($log = false) {
		$dbConfig = require __DIR__ . '/../config/db-config.php';
		$this->db = Database::MySQL($dbConfig);
		$this->config = require __DIR__ . '/../config/config.php';
		$this->log = $log;
	}

	public function insertFile(string $fileName, int $fileSize) {
		$stmt = $this->db->pdo()->prepare('INSERT INTO `files` SET `public_token`=:publictoken, `file_name`=:filename, `file_size`=:filesize');
		$publicToken =  $this->generatePublicToken();

		$stmt->execute([
			':publictoken' => $publicToken,
			':filename' => $fileName,
			':filesize' => $fileSize
		]);

		if ($id = $this->db->pdo()->lastInsertId()) {
			if ($this->log) {
				echo 'File inserted with ID ' . $id . ' and public token ' . $publicToken . PHP_EOL;
			}

			if ($this->config['send_notifications']) {
				$this->sendNotificationEmail($fileName, $publicToken);
			}
			return $id;
		}

		throw new \Exception('file_could_not_be_inserted');
	}

	public function moveUploadedFiles() {
		foreach (new \DirectoryIterator(__DIR__ . '/../../upload') as $fileInfo) {
			if ($fileInfo->isDot() || $fileInfo->isDir()) continue;
			if ($this->checkIfUploadIsDone($fileInfo->getFileName())) {
				if ($fileId = $this->insertFile($fileInfo->getFileName(), $fileInfo->getSize())) {
					if (!is_dir(__DIR__ . '/../../files')) {
						mkdir(__DIR__ . '/../../files');
					}
					mkdir(__DIR__ . '/../../files/' . $fileId);
					rename($fileInfo->getPath() . '/' . $fileInfo->getFileName(), __DIR__ . '/../../files/' . $fileId . '/' . $fileInfo->getFileName());
					if ($this->log) {
						echo 'File moved (' . $fileInfo->getPath() . ' to ' . __DIR__ . '/../../files/' . $fileId . '/' . $fileInfo->getFileName() . ')' . PHP_EOL;
					}
					$stmt = $this->db->pdo()->prepare('DELETE FROM `pending_files` WHERE `file_name`=:filename');
					$stmt->execute([':filename' => $fileInfo->getFileName()]);
				}
			}
		}
	}

	public function checkIfUploadIsDone(string $fileName) {
		$currentFileSize = filesize(__DIR__ . '/../../upload/' . $fileName);

		$stmt = $this->db->pdo()->prepare('SELECT * FROM `pending_files` WHERE `file_name`=:filename');
		$stmt->execute([':filename' => $fileName]);
		if ($result = $stmt->fetchObject()) {
			if ($result->file_size === $currentFileSize) {
				if ($this->log) {
					echo 'File ' . $fileName . ' upload done.' . PHP_EOL;
				}
				return true;
			}
		}

		$stmt = $this->db->pdo()->prepare('INSERT INTO `pending_files` SET `file_name`=:filename, `file_size`=:filesize ON DUPLICATE KEY UPDATE `file_size`=:filesize_2');
		$stmt->execute([
			':filename'		=> $fileName,
			':filesize'		=> $currentFileSize,
			':filesize_2'	=> $currentFileSize
		]);

		return false;
	}

	public function generatePublicToken() {
		$length = $this->config['token_length'] ?: 16;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$token = '';
		for ($i = 0; $i < $length; $i++) {
			$token .= $characters[rand(0, $charactersLength - 1)];
		}

		return $token;
	}

	public function sendNotificationEmail(string $fileName, string $publicToken) {
		if (!$recipients = $this->config['emails']) {
			return;
		}
		if (!$url = $this->config['url']) {
			throw new \Exception('no_url_specified');
		}

		$mail = new \PHPMailer\PHPMailer\PHPMailer();
		$mailConfig = require __DIR__ . '/../config/mail-config.php';

		$mail->CharSet = 'UTF-8';
		$mail->isHTML(true);

		if ($mailConfig['use_smtp']) {
			$mail->IsSMTP();
			$mail->Host = $mailConfig['server'];
			$mail->Port = $mailConfig['port'];
			$mail->SMTPAuth = true;
			$mail->Username = $mailConfig['user'];
			$mail->Password = $mailConfig['password'];
			$mail->SMTPSecure = 'ssl';
		}

		foreach($recipients as $recipient) {
			$mail->addAddress($recipient);
		}

		$mail->setFrom($mailConfig['mail_from'], 'DRPdev cloud');
		$mail->Subject = 'New File Upload';
		$mail->Body = '<p>A new file has been uploaded: ' . htmlspecialchars($fileName) . '</p><p><a href="' . $url . '/f/' . $publicToken . '">' . $url . '/f/' . $publicToken . '</a></p>';

		if (!$mail->send()) {
			throw new \Exception('Error sending email: ' .  $mail->ErrorInfo);
		}
	}
}
