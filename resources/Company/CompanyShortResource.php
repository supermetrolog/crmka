<?php

declare(strict_types=1);

namespace app\resources\Company;

use app\kernel\web\http\resources\JsonResource;
use app\models\Company;

class CompanyShortResource extends JsonResource
{
	private Company $resource;

	public function __construct(Company $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                   => $this->resource->id,
			'nameRu'               => $this->resource->nameRu,
			'nameEng'              => $this->resource->nameEng,
			'noName'               => $this->resource->noName,
			'full_name'            => $this->resource->getFullName(),
			'nameBrand'            => $this->resource->nameBrand,
			'rating'               => $this->resource->rating,
			'description'          => $this->resource->description,
			'formOfOrganization'   => $this->resource->formOfOrganization,
			'officeAdress'         => $this->resource->officeAdress,
			'legalAddress'         => $this->resource->legalAddress,
			'latitude'             => $this->resource->latitude,
			'longitude'            => $this->resource->longitude,
			'companyGroup_id'      => $this->resource->companyGroup_id,
			'activityGroup'        => $this->resource->activityGroup,
			'activityProfile'      => $this->resource->activityProfile,
			'status'               => $this->resource->status,
			'active'               => $this->resource->active,
			'processed'            => $this->resource->processed,
			'passive_why'          => $this->resource->passive_why,
			'passive_why_comment'  => $this->resource->passive_why_comment,
			'consultant_id'        => $this->resource->consultant_id,
			'is_individual'        => $this->resource->is_individual,
			'individual_full_name' => $this->resource->individual_full_name,
			'show_product_ranges'  => $this->resource->show_product_ranges,
			'created_at'           => $this->resource->created_at,
			'updated_at'           => $this->resource->updated_at
		];
	}
}