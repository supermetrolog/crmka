<?php

declare(strict_types=1);

namespace app\dto\Telegram;

use yii\base\BaseObject;

class StatusLinkTelegramDto extends BaseObject
{
	public bool    $linked;
	public ?string $username;
	public ?string $firstName;
	public ?string $lastName;
	public bool    $isLoginEnabled;
}