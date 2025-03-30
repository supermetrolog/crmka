<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Company;
use app\resources\Company\CompanyBaseResource;

class RequestSearchCompanyResource extends JsonResource
{
	private Company $resource;

	public function __construct(Company $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			CompanyBaseResource::make($this->resource)->toArray(),
			[
				'objects_count'         => $this->resource->getObjectsCount(),
				'requests_count'        => $this->resource->getRequestsCount(),
				'active_requests_count' => $this->resource->getActiveRequestsCount(),
				'contacts_count'        => $this->resource->getContactsCount(),
				'active_contacts_count' => $this->resource->getActiveContactsCount()
			]
		);
	}
}