<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TCallbackQuery extends TModel
{
	protected array $casts = [
		'from'    => TUser::class,
		'message' => TMessage::class,
	];

	public string    $id;
	public TUser     $from;
	public ?TMessage $message           = null;
	public ?string   $inline_message_id = null;
	public ?string   $data              = null;
}
