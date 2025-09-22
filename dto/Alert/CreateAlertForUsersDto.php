<?php

declare(strict_types=1);

namespace app\dto\Alert;

use app\models\User\User;
use yii\base\BaseObject;

class CreateAlertForUsersDto extends BaseObject
{
	public string $message;
	public string $created_by_type;
	public int    $created_by_id;

	/**
	 * @var User[]
	 */
	public array $users;
}