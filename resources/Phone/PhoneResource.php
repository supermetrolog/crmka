<?php

declare(strict_types=1);

namespace app\resources\Phone;

use app\helpers\PhoneHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\Phone;
use libphonenumber\PhoneNumberFormat;

class PhoneResource extends JsonResource
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
			'contact_id'   => $this->resource->contact_id,
			'native_phone' => $this->resource->phone,
			'phone'        => PhoneHelper::tryFormat($this->resource->phone, PhoneNumberFormat::NATIONAL, $this->resource->country_code),
			'tel'          => PhoneHelper::tryFormat($this->resource->phone, PhoneNumberFormat::RFC3966, $this->resource->country_code),
			'exten'        => $this->resource->exten,
			'isMain'       => $this->resource->isMain,
			'type'         => $this->resource->type,
			'comment'      => $this->resource->comment,
			'country_code' => $this->resource->country_code,
			'status'       => $this->resource->status,
			'created_at'   => $this->resource->created_at,
			'updated_at'   => $this->resource->updated_at,
			'deleted_at'   => $this->resource->deleted_at,
		];
	}
}