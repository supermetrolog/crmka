<?php

declare(strict_types=1);

namespace app\dto\Notification;

use app\components\Notification\Interfaces\NotifiableInterface;
use app\dto\Notification\NotificationRelationDto;
use app\dto\Notification\NotificationActionDto;
use yii\base\BaseObject;

class CreateNotificationDto extends BaseObject
{
	public string $channel;
	public string $kind;
	public string $subject;
	public string $message;
	public NotifiableInterface $notifiable;
	public string $created_by_type;
	public int $created_by_id;
	
	/** @var NotificationRelationDto[] */
	public array $relations = [];
	
	/** @var NotificationActionDto[] */
	public array $actions = [];
}
