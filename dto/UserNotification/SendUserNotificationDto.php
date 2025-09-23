<?php

declare(strict_types=1);

namespace app\dto\UserNotification;

use DateTimeInterface;
use yii\base\BaseObject;

class SendUserNotificationDto extends BaseObject
{
	public string             $subject;
	public string             $message;
	public int                $userId;
	public string             $channel;
	public ?int               $templateId;
	public ?DateTimeInterface $expiresAt;

	public ?string $createdByType = null;
	public ?int    $createdById   = null;
}