<?php

declare(strict_types=1);

namespace app\dto\User;

use yii\base\BaseObject;

class UserTelegramLinkDto extends BaseObject
{
	public int     $userId;
	public int     $telegramUserId;
	public string  $chatId;
	public ?string $username;
	public ?string $firstName;
	public ?string $lastName;
	public bool    $isEnabled;
}