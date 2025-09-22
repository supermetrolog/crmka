<?php

declare(strict_types=1);

namespace app\resources\Whatsapp;

use app\dto\Whatsapp\StatusLinkWhatsappDto;
use app\kernel\web\http\resources\JsonResource;

class StatusWhatsappLinkResource extends JsonResource
{
	private StatusLinkWhatsappDto $resource;

	public function __construct(StatusLinkWhatsappDto $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'linked'     => $this->resource->linked,
			'phone'      => $this->resource->phone,
			'first_name' => $this->resource->firstName,
			'full_name'  => $this->resource->fullName,
			'push_name'  => $this->resource->pushName,
		];
	}
}