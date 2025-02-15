<?php

declare(strict_types=1);

namespace app\resources\Company\ActivityGroup;

use app\kernel\web\http\resources\JsonResource;
use app\models\CompanyActivityGroup;

class CompanyActivityGroupResource extends JsonResource
{
	private CompanyActivityGroup $resource;

	public function __construct(CompanyActivityGroup $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                => $this->resource->id,
			'activity_group_id' => $this->resource->activity_group_id
		];
	}
}