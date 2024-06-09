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

	public function getPath(): string
	{
		return $this->path;
	}

	public function setPath(string $path): self
	{
		$this->path = $path;

		return $this;
	}

	public function fileExists(): bool
	{
		return file_exists($this->path);
	}

	public function unlink(): void
	{
		if (!$this->fileExists()) {
			return;
		}

		unlink($this->path);
	}
}