<?php

namespace app\components;

class PathBuilder
{
	public function join(string $basePath, string $path): string
	{
		if (empty($basePath)) {
			return ltrim($path, DIRECTORY_SEPARATOR);
		}

		return rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
	}
}
