<?php

declare(strict_types=1);

namespace app\components\Integrations\Whatsapp\Models;

use app\components\Integrations\IntegrationModel;

class WProfileContact extends IntegrationModel
{
	public bool    $Found;
	public ?string $FirstName;
	public ?string $FullName;
	public ?string $PushName;
	public ?string $BusinessName;
}
