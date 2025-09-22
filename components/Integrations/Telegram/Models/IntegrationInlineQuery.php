<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationInlineQuery extends IntegrationModel
{
	protected array $casts = [
		'from' => IntegrationUser::class
	];

	public int              $id;
	public ?IntegrationUser $from = null;
	public string           $query;
	public string           $offset;
}
