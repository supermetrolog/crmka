<?php

declare(strict_types=1);

namespace app\components\Integrations\Whatsapp\Models\Response;

use app\components\Integrations\IntegrationModel;

class WResponse extends IntegrationModel
{
	public string $status;
	public int    $timestamp;
	public string $time;
}
