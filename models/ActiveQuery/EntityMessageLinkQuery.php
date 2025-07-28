<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\EntityMessageLink;

class EntityMessageLinkQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return EntityMessageLink[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?EntityMessageLink
	{
		/** @var ?EntityMessageLink */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): EntityMessageLink
	{
		/** @var EntityMessageLink */
		return parent::oneOrThrow($db);
	}

	public function byEntityId(int $entityId): EntityMessageLinkQuery
	{
		return $this->andWhere(['entity_id' => $entityId]);
	}

	public function byEntityType(string $entityType): EntityMessageLinkQuery
	{
		return $this->andWhere(['entity_type' => $entityType]);
	}

	public function byEntity(int $entityId, string $entityType): EntityMessageLinkQuery
	{
		return $this->byEntityId($entityId)->byEntityType($entityType);
	}

	public function byKind(string $kind): EntityMessageLinkQuery
	{
		return $this->andWhere(['kind' => $kind]);
	}

	public function byChatMemberMessageId(int $chatMemberMessageId): EntityMessageLinkQuery
	{
		return $this->andWhere(['chat_member_message_id' => $chatMemberMessageId]);
	}
}