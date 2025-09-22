<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationChosenInlineResult extends IntegrationModel
{
	protected array $casts = [
		'from' => IntegrationUser::class
	];

	public string          $result_id;
	public IntegrationUser $from;
	public ?string         $inline_message_id = null;
	public string          $query;
}
