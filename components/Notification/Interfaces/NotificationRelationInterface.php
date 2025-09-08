<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface NotificationRelationInterface
{
	public function getEntityType(): string;

	public function getEntityId(): int;
}