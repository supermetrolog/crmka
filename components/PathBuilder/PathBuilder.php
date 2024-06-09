<?php

namespace app\components\PathBuilder;

class PathBuilder
{
	private array $parts = [];

	public function addPart(string $part): self
	{
		$this->parts[] = trim($part, DIRECTORY_SEPARATOR . " \t\n\r\0\x0B");

		return $this;
	}

	public function build(): Path
	{
		return new Path(implode(DIRECTORY_SEPARATOR, $this->parts));
	}
}
