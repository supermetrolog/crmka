<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationUser extends IntegrationModel
{
	public int     $id;
	public string  $first_name;
	public ?string $last_name = null;
	public ?string $username  = null;
}
