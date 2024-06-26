<?php

declare(strict_types=1);

namespace app\components\archiver;

class File
{
	private string $filename;
	private string $content;

	public function __construct(string $filename, string $content)
	{
		$this->filename = $filename;
		$this->content  = $content;
	}

	public function getFilename(): string
	{
		return $this->filename;
	}

	public function getContent(): string
	{
		return $this->content;
	}
}