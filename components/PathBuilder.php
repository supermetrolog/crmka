<?php

namespace app\components;

class PathBuilder
{
	public function join(string $basePath, string $path): string
	{
		return rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
	}
}
