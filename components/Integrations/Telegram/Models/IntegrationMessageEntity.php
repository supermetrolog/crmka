<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationMessageEntity extends IntegrationModel
{
	protected array $casts = [
		'user' => IntegrationUser::class
	];

	public string           $type;
	public int              $offset;
	public int              $length;
	public ?string          $url  = null;
	public ?IntegrationUser $user = null;
}
