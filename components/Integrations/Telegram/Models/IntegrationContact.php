<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationContact extends IntegrationModel
{
	public string  $phone_number;
	public string  $first_name;
	public ?string $last_name = null;
	public ?int    $user_id   = null;
}
