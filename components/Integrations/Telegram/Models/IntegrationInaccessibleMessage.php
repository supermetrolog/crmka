<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationInaccessibleMessage extends IntegrationModel
{
	protected array $casts = [
		'chat' => IntegrationChat::class
	];

	public IntegrationChat $chat;
	public int             $message_id;
	public int             $date;
}
