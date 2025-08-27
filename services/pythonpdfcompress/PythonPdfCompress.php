<?php

namespace app\services\pythonpdfcompress;

use app\helpers\StringHelper;
use Exception;
use RuntimeException;

class PythonPdfCompress
{
	public const COMPRESS_TYPE_VERY_HIGH = 4;
	public const COMPRESS_TYPE_HIGH      = 3;
	public const COMPRESS_TYPE_AVERAGE   = 2;
	public const COMPRESS_TYPE_LOW       = 1;


	private $outpath;
	private $inpath;
	private $compressLevel;
	private $pythonScriptPath;
	private $pythonPath;
	private $error;

	public function __construct($python_path, $python_script_path, $inpath, $outpath, $compress_level = self::COMPRESS_TYPE_HIGH)
	{
		$this->pythonPath       = $python_path;
		$this->pythonScriptPath = $python_script_path;
		$this->outpath          = $outpath;
		$this->inpath           = $inpath;
		$this->compressLevel    = $compress_level;
	}

	private function validate(): bool
	{
		if (!$this->pythonScriptPath || !$this->outpath || !$this->inpath) {
			$this->error = "properties cannot be null";

			return false;
		}

		if (!file_exists($this->inpath)) {
			$this->error = "inpath file not exist";

			return false;
		}

		if ($this->inpath === $this->outpath) {
			$this->error = "inpath and outpath cannot be equal";

			return false;
		}

		return true;
	}

	private function validateOrThrow(): void
	{
		if (!$this->validate()) {
			throw new RuntimeException("Validate error: " . $this->error);
		}
	}

	/**
	 * @throws Exception
	 */
	public function Compress(): void
	{
		$this->validateOrThrow();

		$cmd = $this->getCmdString();

		$output     = [];
		$returnCode = null;

		exec($cmd, $output, $returnCode);

		if ($returnCode) {
			throw new RuntimeException("Compression failed: " . StringHelper::join(', ', ...$output) . " (code $returnCode)");
		}

		if (!file_exists($this->outpath)) {
			throw new RuntimeException("Compression script did not produce output file: $this->outpath");
		}
	}

	public function replaceOriginalFile(): void
	{
		if (!file_exists($this->outpath)) {
			throw new RuntimeException("Cannot replace original: output file not found ($this->outpath)");
		}

		if (file_exists($this->inpath)) {
			$this->removeOriginalFile();
		}

		$this->renameFile();
	}

	public function renameFile(): bool
	{
		return rename($this->outpath, $this->inpath);
	}

	public function removeOriginalFile(): bool
	{
		return @unlink($this->inpath);
	}

	private function getCmdString(): string
	{
		return "{$this->pythonPath} {$this->pythonScriptPath} -o {$this->outpath} -c {$this->compressLevel} {$this->inpath}";
	}
}
