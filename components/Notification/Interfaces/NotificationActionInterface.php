<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

use DateTimeInterface;

interface NotificationActionInterface
{
	public function getType(): string;

	public function getCode(): string;

	public function getLabel(): string;

	public function getIcon(): ?string;

	public function getStyle(): ?string;

	public function needConfirmation(): bool;

	public function getOrder(): int;

	public function getExpiresAt(): ?DateTimeInterface;

	public function getPayloadArray(): ?array;
}