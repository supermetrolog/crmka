<?php

declare(strict_types=1);

namespace app\components\Notification\Builders;

use app\components\Notification\NotificationAction;
use app\components\Notification\NotificationActionNavigatePayload;
use app\enum\Notification\UserNotificationActionStyleEnum;
use app\enum\Notification\UserNotificationActionTypeEnum;
use app\enum\Ui\UiIconEnum;
use DateTimeInterface;

class NotificationActionBuilder
{
	private string             $type;
	private string             $code         = 'base';
	private string             $label        = 'Действие';
	private ?string            $icon         = null;
	private ?string            $style        = null;
	private bool               $confirmation = false;
	private int                $order        = 0;
	private ?DateTimeInterface $expiresAt    = null;
	private ?array             $payload      = null;

	public static function type(string $type): self
	{
		$b       = new self();
		$b->type = $type;

		return $b;
	}

	protected static function navigate(): self
	{
		$b = self::type(UserNotificationActionTypeEnum::NAVIGATE);

		$b->label('Перейти');
		$b->code('navigate');

		$b->icon(UiIconEnum::LINK);
		$b->style(UserNotificationActionStyleEnum::LIGHT);

		return $b;
	}

	public static function navigateToUrl(string $url): self
	{
		$b = self::navigate();

		$b->payload(NotificationActionNavigatePayload::toUrl($url)->toArray());

		return $b;
	}

	public static function navigateToRoute(
		string $routeCode,
		?array $params = null,
		?array $query = null,
		?string $fallbackUrl = null): self
	{
		$b = self::navigate();

		$b->payload(NotificationActionNavigatePayload::toRoute($routeCode, $params, $query, $fallbackUrl)->toArray());

		return $b;
	}

	public static function command(): self
	{
		return self::type(UserNotificationActionTypeEnum::COMMAND);
	}

	public function code(string $v): self
	{
		$this->code = $v;

		return $this;
	}

	public function label(string $v): self
	{
		$this->label = $v;

		return $this;
	}

	public function icon(?string $v): self
	{
		$this->icon = $v;

		return $this;
	}

	public function style(?string $v): self
	{
		$this->style = $v;

		return $this;
	}

	public function confirm(bool $v = true): self
	{
		$this->confirmation = $v;

		return $this;
	}

	public function order(int $v): self
	{
		$this->order = $v;

		return $this;
	}

	public function expires(?DateTimeInterface $v): self
	{
		$this->expiresAt = $v;

		return $this;
	}

	public function payload(?array $v): self
	{
		$this->payload = $v;

		return $this;
	}

	public function build(): NotificationAction
	{
		return new NotificationAction(
			$this->type, $this->code, $this->label, $this->icon, $this->style,
			$this->confirmation, $this->order, $this->expiresAt, $this->payload
		);
	}
}