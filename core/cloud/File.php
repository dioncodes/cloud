<?php

namespace drpdev\Cloud;

class File {

	/**
	 * file id
	 *
	 * @var int
	 */
	private $id;

	/**
	 * public token
	 *
	 * @var string
	 */
	private $publicToken;

	/**
	 * file name
	 *
	 * @var string
	 */
	private $fileName;

	/**
	 * file size (byte)
	 *
	 * @var int
	 */
	private $fileSize;

	/**
	 * upload date (YYYY-MM-DD)
	 *
	 * @var string
	 */
	private $uploadDate;

	public function __construct(int $id, string $fileName, int $fileSize, string $uploadDate = null, string $publicToken = null) {
		$this->id = $id;
		$this->publicToken = $publicToken;
		$this->fileName = $fileName;
		$this->fileSize = $fileSize;
		$this->uploadDate = $uploadDate;
	}

	public function getFormattedFileSize() {
		if ($this->fileSize > 1000000000) {
			return number_format($this->fileSize / 1000000000, 2, '.', '') . ' GB';
		} elseif ($this->fileSize > 1000000) {
			return number_format($this->fileSize / 1000000, 2, '.', '') . ' MB';
		} elseif ($this->fileSize > 1000) {
			return number_format($this->fileSize / 1000, 2, '.', '') . ' KB';
		}
		return $this->fileSize . ' Byte';
	}

	/**
	 * Get file id
	 *
	 * @return  int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set file id
	 *
	 * @param  int  $id  file id
	 *
	 * @return  self
	 */
	public function setId(int $id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Get file name
	 *
	 * @return  string
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * Set file name
	 *
	 * @param  string  $fileName  file name
	 *
	 * @return  self
	 */
	public function setFileName(string $fileName) {
		$this->fileName = $fileName;

		return $this;
	}

	/**
	 * Get file size (byte)
	 *
	 * @return  int
	 */
	public function getFileSize() {
		return $this->fileSize;
	}

	/**
	 * Set file size (byte)
	 *
	 * @param  int  $fileSize  file size (byte)
	 *
	 * @return  self
	 */
	public function setFileSize(int $fileSize) {
		$this->fileSize = $fileSize;

		return $this;
	}

	/**
	 * Get upload date (YYYY-MM-DD)
	 *
	 * @return  string
	 */
	public function getUploadDate() {
		return $this->uploadDate;
	}

	/**
	 * Set upload date (YYYY-MM-DD)
	 *
	 * @param  string  $uploadDate  upload date (YYYY-MM-DD)
	 *
	 * @return  self
	 */
	public function setUploadDate(string $uploadDate) {
		$this->uploadDate = $uploadDate;

		return $this;
	}

	/**
	 * Get public token
	 *
	 * @return  string
	 */
	public function getPublicToken()
	{
		return $this->publicToken;
	}

	/**
	 * Set public token
	 *
	 * @param  string  $publicToken  public token
	 *
	 * @return  self
	 */
	public function setPublicToken(string $publicToken)
	{
		$this->publicToken = $publicToken;

		return $this;
	}
}
