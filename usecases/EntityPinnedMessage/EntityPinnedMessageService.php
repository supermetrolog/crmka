<?php

declare(strict_types=1);

namespace app\usecases\EntityPinnedMessage;

use app\dto\EntityPinnedMessage\EntityPinnedMessageDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\EntityPinnedMessage;
use Throwable;
use yii\db\StaleObjectException;

class EntityPinnedMessageService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(EntityPinnedMessageDto $dto): EntityPinnedMessage
	{
		$model = new EntityPinnedMessage([
			'entity_id'              => $dto->entity_id,
			'entity_type'            => $dto->entity_type,
			'chat_member_message_id' => $dto->message->id,
			'created_by_id'          => $dto->user->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(EntityPinnedMessage $model): void
	{
		$model->delete();
	}
}