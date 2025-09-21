<?php

declare(strict_types=1);

namespace app\dto\Telegram;

use yii\base\BaseObject;

class TelegramUserDataDto extends BaseObject
{
	public int     $telegramUserId;
	public ?int    $telegramChatId = null;
	public ?string $username       = null;
	public ?string $firstName;
	public ?string $lastName;
}