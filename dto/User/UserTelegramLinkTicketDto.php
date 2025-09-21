<?php

declare(strict_types=1);

namespace app\dto\User;

use DateTimeInterface;
use yii\base\BaseObject;

class UserTelegramLinkTicketDto extends BaseObject
{
	public int               $userId;
	public string            $code;
	public DateTimeInterface $expiresAt;
}