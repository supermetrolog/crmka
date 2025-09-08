<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface NotificationTemplateInterface
{
	public function getKind(): string;

	public function getCategory(): string;

	public function getPriority(): string;

	public function isActive(): bool;
}