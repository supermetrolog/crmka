<?php

declare(strict_types=1);

namespace app\resources\Company;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Company;
use app\resources\Company\Category\CompanyCategoryResource;
use app\resources\Company\File\CompanyFileResource;
use app\resources\Company\Group\CompanyGroupResource;
use app\resources\Company\ProductRange\CompanyProductRangeResource;
use app\resources\Contact\ContactResource;

class CompanyViewResource extends JsonResource
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
				'contacts'          => ContactResource::collection($this->resource->contacts),
				'categories'        => CompanyCategoryResource::collection($this->resource->categories),
				'productRanges'     => CompanyProductRangeResource::collection($this->resource->productRanges),
				'companyGroup'      => CompanyGroupResource::tryMakeArray($this->resource->companyGroup),
				'files'             => CompanyFileResource::collection($this->resource->files),
				'logo'              => $this->resource->getLogoUrl(),
				'dealsRequestEmpty' => $this->resource->dealsRequestEmpty,
				'objects_count'     => $this->resource->getObjectsCount(),
				'requests_count'    => $this->resource->getRequestsCount()
			]
		);
	}
}