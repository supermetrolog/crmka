<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TInaccessibleMessage extends TModel
{
	protected array $casts = [
		'chat' => TChat::class
	];

	public TChat $chat;
	public int   $message_id;
	public int   $date;
}
