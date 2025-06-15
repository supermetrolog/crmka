<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\EntityPinnedMessage;

class EntityPinnedMessageRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): EntityPinnedMessage
	{
		return EntityPinnedMessage::find()->byId($id)->oneOrThrow();
	}
}