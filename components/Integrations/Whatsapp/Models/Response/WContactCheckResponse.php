<?php

declare(strict_types=1);

namespace app\components\Integrations\Whatsapp\Models\Response;

class WContactCheckResponse extends WResponse
{
	public bool   $on_whatsapp;
	public string $phone;
}
