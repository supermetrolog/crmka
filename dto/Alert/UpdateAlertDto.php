<?php

declare(strict_types=1);

namespace app\dto\Alert;

use app\models\User;
use DateTimeInterface;
use yii\base\BaseObject;

class UpdateAlertDto extends BaseObject
{
	public User   $user;
	public string $message;
}