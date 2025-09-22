<?php

declare(strict_types=1);

namespace app\components\Integrations\Whatsapp\Models;

use app\components\Integrations\IntegrationModel;

class WProfileUser extends IntegrationModel
{
	public ?string $VerifiedName;
	public ?string $Status;
	public ?string $PictureID;

	/** @var array<string> */
	public array $Devices = [];
}
