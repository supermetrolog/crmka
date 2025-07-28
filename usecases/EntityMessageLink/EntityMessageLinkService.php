<?php

declare(strict_types=1);

namespace app\usecases\EntityMessageLink;

use app\dto\EntityMessageLink\EntityMessageLinkDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\EntityMessageLink;
use Throwable;
use yii\db\StaleObjectException;

class EntityMessageLinkService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(EntityMessageLinkDto $dto): EntityMessageLink
	{
		$model = new EntityMessageLink([
			'entity_id'              => $dto->entity_id,
			'entity_type'            => $dto->entity_type,
			'chat_member_message_id' => $dto->message->id,
			'created_by_id'          => $dto->user->id,
			'kind'                   => $dto->kind,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function createIfNotExists(EntityMessageLinkDto $dto): EntityMessageLink
	{
		$existing = EntityMessageLink::find()->byKind($dto->kind)->byEntity($dto->entity_id, $dto->entity_type)->byChatMemberMessageId($dto->message->id)->one();

		if ($existing) {
			return $existing;
		}

		return $this->create($dto);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(EntityMessageLink $model): void
	{
		$model->delete();
	}
}