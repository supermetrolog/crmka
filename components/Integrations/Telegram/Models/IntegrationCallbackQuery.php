<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationCallbackQuery extends IntegrationModel
{
	protected array $casts = [
		'from'    => IntegrationUser::class,
		'message' => IntegrationMessage::class,
	];

	public string              $id;
	public IntegrationUser     $from;
	public ?IntegrationMessage $message           = null;
	public ?string             $inline_message_id = null;
	public ?string             $data              = null;
}
