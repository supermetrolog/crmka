<?php

declare(strict_types=1);

namespace app\dto\User;

use yii\base\BaseObject;

class UpdateUserProfileDto extends BaseObject
{
	public ?string $first_name;
	public ?string $middle_name;
	public ?string $last_name;
	public ?string $caller_id;
	public array   $emails;
	public array   $phones;
	public string  $gender;
}