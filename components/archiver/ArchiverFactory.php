<?php

declare(strict_types=1);

namespace app\components\archiver;

use yii\base\ErrorException;

class ArchiverFactory
{
	/**
	 * @throws ErrorException
	 */
	public function create(string $filename): Archiver
	{
		return new Archiver($filename);
	}
}