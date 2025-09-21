<?php

declare(strict_types=1);

namespace app\components\Telegram\Models;

class TUpdate extends TModel
{
	protected array $casts = [
		'message'              => TMessage::class,
		'edited_message'       => TMessage::class,
		'inline_query'         => TInlineQuery::class,
		'chosen_inline_result' => TChosenInlineResult::class,
		'callback_query'       => TCallbackQuery::class
	];

	public int                  $update_id;
	public ?TMessage            $message              = null;
	public ?TMessage            $edited_message       = null;
	public ?TInlineQuery        $inline_query         = null;
	public ?TChosenInlineResult $chosen_inline_result = null;
	public ?TCallbackQuery      $callback_query       = null;
}
