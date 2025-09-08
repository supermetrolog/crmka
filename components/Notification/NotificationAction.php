<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Interfaces\NotificationActionInterface;
use DateTimeInterface;

class NotificationAction implements NotificationActionInterface
{
	public string             $type;
	public string             $code;
	public string             $label;
	public ?string            $icon;
	public ?string            $style;
	public bool               $confirmation;
	public int                $order;
	public ?DateTimeInterface $expires_at;
	public ?array             $payload;

	public function __construct(string $type, string $code, string $label, ?string $icon, ?string $style, bool $needConfirmation, int $order, ?DateTimeInterface $expiresAt, ?array $payload)
	{
		$this->type         = $type;
		$this->code         = $code;
		$this->label        = $label;
		$this->icon         = $icon;
		$this->style        = $style;
		$this->confirmation = $needConfirmation;
		$this->order        = $order;
		$this->expires_at   = $expiresAt;
		$this->payload      = $payload;
	}

	public function needConfirmation(): bool
	{
		return $this->confirmation;
	}

	public function getPayloadArray(): array
	{
		return $this->payload;
	}

	public function getExpiresAt(): ?DateTimeInterface
	{
		return $this->expires_at;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function getIcon(): string
	{
		return $this->icon;
	}

	public function getStyle(): string
	{
		return $this->style;
	}

	public function getOrder(): int
	{
		return $this->order;
	}
}