<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationUpdate extends IntegrationModel
{
	protected array $casts = [
		'message'              => IntegrationMessage::class,
		'edited_message'       => IntegrationMessage::class,
		'inline_query'         => IntegrationInlineQuery::class,
		'chosen_inline_result' => IntegrationChosenInlineResult::class,
		'callback_query'       => IntegrationCallbackQuery::class
	];

	public int                            $update_id;
	public ?IntegrationMessage            $message              = null;
	public ?IntegrationMessage            $edited_message       = null;
	public ?IntegrationInlineQuery        $inline_query         = null;
	public ?IntegrationChosenInlineResult $chosen_inline_result = null;
	public ?IntegrationCallbackQuery      $callback_query       = null;
}
