<?php

declare(strict_types=1);

namespace app\components\PathBuilder;

class PathBuilderFactory
{

	public function create(): PathBuilder
	{
		return new PathBuilder();
	}
}