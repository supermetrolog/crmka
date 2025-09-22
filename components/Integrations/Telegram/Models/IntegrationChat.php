<?php

declare(strict_types=1);

namespace app\components\Integrations\Telegram\Models;

use app\components\Integrations\IntegrationModel;

class IntegrationChat extends IntegrationModel
{
	public int     $id;
	public string  $type;
	public ?string $title      = null;
	public ?string $username   = null;
	public ?string $first_name = null;
	public ?string $last_name  = null;
}
