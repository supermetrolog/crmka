<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\CompanyPinnedMessage;

class CompanyPinnedMessageRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): CompanyPinnedMessage
	{
		return CompanyPinnedMessage::find()->byId($id)->oneOrThrow();
	}
}