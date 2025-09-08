<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Interfaces\NotificationRelationInterface;
use app\kernel\common\models\AR\AR;

class NotificationRelation implements NotificationRelationInterface
{
	public string $entity_type;
	public int    $entity_id;

	public function __construct(string $entityType, int $entityId)
	{
		$this->entity_type = $entityType;
		$this->entity_id   = $entityId;
	}

	public function getEntityType(): string
	{
		return $this->entity_type;
	}

	public function getEntityId(): int
	{
		return $this->entity_id;
	}

	public static function from(AR $record): self
	{
		return new self($record::getMorphClass(), $record->getPrimaryKey());
	}
}