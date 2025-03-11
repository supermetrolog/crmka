<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Request;

class RequestRepository
{
	public function findOne(int $id): ?Request
	{
		return Request::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Request
	{
		return Request::find()->byId($id)->oneOrThrow();
	}
}