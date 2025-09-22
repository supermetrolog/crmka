<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TChosenInlineResult extends TModel
{
	protected array $casts = [
		'from' => TUser::class
	];

	public string  $result_id;
	public TUser   $from;
	public ?string $inline_message_id = null;
	public string  $query;
}
