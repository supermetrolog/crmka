<?php

declare(strict_types=1);

namespace app\components\Integrations\Whatsapp\Models\Response;

use app\components\Integrations\Whatsapp\Models\WProfile;

class WContactInfoResponse extends WResponse
{
	protected array  $casts = [
		'profile' => WProfile::class,
	];
	public ?WProfile $profile;
}
