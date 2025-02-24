<?php

declare(strict_types=1);

namespace app\resources\Company\ActivityProfile;

use app\kernel\web\http\resources\JsonResource;
use app\models\CompanyActivityProfile;

class CompanyActivityProfileResource extends JsonResource
{
	private CompanyActivityProfile $resource;

	public function __construct(CompanyActivityProfile $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                  => $this->resource->id,
			'activity_profile_id' => $this->resource->activity_profile_id
		];
	}
}