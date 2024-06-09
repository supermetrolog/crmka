<?php

declare(strict_types=1);

namespace app\components\PathBuilder;

class Path
{
	private string $path;

	public function __construct(string $path)
	{
		$this->path = $path;
	}

	public function getRel(): string
	{
		return $this->path;
	}

	public function getAbs(): string
	{
		return DIRECTORY_SEPARATOR . $this->path;
	}

	public function setPath(string $path): self
	{
		$this->path = $path;

		return $this;
	}

	public function fileExists(): bool
	{
		return file_exists($this->getAbs());
	}

	public function unlink(): void
	{
		if (!$this->fileExists()) {
			return;
		}

		unlink($this->getAbs());
	}
}