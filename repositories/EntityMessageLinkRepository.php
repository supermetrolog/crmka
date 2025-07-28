<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\EntityMessageLink;

class EntityMessageLinkRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): EntityMessageLink
	{
		return EntityMessageLink::find()->byId($id)->oneOrThrow();
	}
}