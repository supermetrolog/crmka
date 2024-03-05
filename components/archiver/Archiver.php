<?php

declare(strict_types=1);

namespace app\components\archiver;

use yii\base\ErrorException;
use ZipArchive;

class Archiver
{
	private ZipArchive $zip;

	/** @var File[] */
	private array $files;

	private bool $isClosed = false;

	/**
	 * @throws ErrorException
	 */
	public function __construct(string $filename)
	{
		$this->zip = new ZipArchive();
		$this->open($filename);
	}

	public function __destruct()
	{
		$this->close();
	}

	public function close(): void
	{
		if (!$this->isClosed) {
			$this->zip->close();
			$this->isClosed = true;
		}
	}

	/**
	 * @throws ErrorException
	 */
	private function open(string $filename): void
	{
		$flags = ZipArchive::CREATE;

		if (file_exists($filename)) {
			$flags = ZipArchive::OVERWRITE;
		}

		if (!$this->zip->open($filename, $flags)) {
			throw new ErrorException(sprintf('Open zip file error. Filename: %s', $filename));
		}
	}

	public function add(File $file): void
	{
		$this->files[] = $file;
	}

	/**
	 * @throws ErrorException
	 */
	public function save(): void
	{
		foreach ($this->files as $file) {
			if (!$this->zip->addFromString($file->getFilename(), $file->getContent())) {
				throw new ErrorException(sprintf('Error add from string in zip archive. Filename: %s', $file->getFilename()));
			}
		}

		$this->close();
	}
}