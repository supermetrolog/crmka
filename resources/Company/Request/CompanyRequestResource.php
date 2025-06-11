<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\Request;

class CompanyRequestResource extends JsonResource
{
	private Request $resource;

	public function __construct(Request $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'status'      => $this->resource->status,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,
			'format_name' => $this->resource->getFormatName()
		];
	}
}