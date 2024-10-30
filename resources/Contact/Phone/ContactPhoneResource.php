<?php

declare(strict_types=1);

namespace app\resources\Contact\Phone;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\Phone;

class ContactPhoneResource extends JsonResource
{
	private Phone $resource;

	public function __construct(Phone $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'           => $this->resource->id,
			'native_phone' => $this->resource->phone,
			'phone'        => $this->resource->toFormattedPhone(),
			'exten'        => $this->resource->exten,
			'isMain'       => $this->resource->isMain,
		];
	}
}