<?php

declare(strict_types=1);

namespace app\dto\Telegram;

use yii\base\BaseObject;

class StartLinkTelegramDto extends BaseObject
{
	public string $deepLink;
	public string $code;
	public string $expiresAt;
}