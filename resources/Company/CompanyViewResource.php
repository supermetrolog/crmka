<?php

declare(strict_types=1);

namespace app\resources\Company;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Company;
use app\resources\Company\Category\CompanyCategoryResource;
use app\resources\Company\File\CompanyFileResource;
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
				'files'             => CompanyFileResource::collection($this->resource->files),
				'logo'              => $this->getLogo(),
				'dealsRequestEmpty' => $this->resource->dealsRequestEmpty
			]
		);
	}

	public function getLogo(): ?string
	{
		return $this->resource->logo ? $this->resource->logo->src : null;
	}
}