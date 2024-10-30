<?php

declare(strict_types=1);

namespace app\resources\Contact\Website;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\Website;

class ContactWebsiteResource extends JsonResource
{
	private Website $resource;

	public function __construct(Website $resource)
	{
		$this->resource = $resource;
	}


	public function toArray(): array
	{
		return [
			'id'      => $this->resource->id,
			'website' => $this->resource->website
		];
	}
}