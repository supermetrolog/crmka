<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use DateTimeInterface;
use yii\base\BaseObject;

class UserNotificationActionDto extends BaseObject
{
	public ?int $notificationId = null;

	public string             $code;
	public string             $type;
	public string             $label;
	public int                $order;
	public ?string            $icon;
	public ?string            $style;
	public bool               $confirmation;
	public ?DateTimeInterface $expiresAt = null;
	public ?array             $payload   = null;
}