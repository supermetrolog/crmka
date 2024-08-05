<?php

declare(strict_types=1);

namespace app\dto\Alert;

use app\models\User;
use DateTimeInterface;
use yii\base\BaseObject;

class CreateAlertDto extends BaseObject
{
	public User   $user;
	public string $message;
	public string $created_by_type;
	public int    $created_by_id;
}