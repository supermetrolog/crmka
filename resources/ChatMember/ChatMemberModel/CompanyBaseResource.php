<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\Company;
use app\resources\Company\Category\CompanyCategoryResource;
use app\resources\Company\Group\CompanyGroupResource;

class CompanyBaseResource extends JsonResource
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
			'nameEng'              => $this->resource->nameEng,
			'nameRu'               => $this->resource->nameRu,
			'noName'               => $this->resource->noName,
			'is_individual'        => $this->resource->is_individual,
			'individual_full_name' => $this->resource->individual_full_name,
			'activityGroup'        => $this->resource->activityGroup,
			'activityProfile'      => $this->resource->activityProfile,
			'consultant_id'        => $this->resource->consultant_id,
			'consultant'           => UserShortResource::tryMakeArray($this->resource->consultant),
			'full_name'            => $this->resource->getFullName(),
			'logo'                 => $this->resource->getLogoUrl(),
			'categories'           => CompanyCategoryResource::collection($this->resource->categories),
			'companyGroup_id'      => $this->resource->companyGroup_id,
			'companyGroup'         => CompanyGroupResource::tryMakeArray($this->resource->companyGroup),
			'office_address'       => $this->resource->officeAdress,
			'legal_address'        => $this->resource->legalAddress,
			'created_at'           => $this->resource->created_at,
			'updated_at'           => $this->resource->updated_at
		];
	}
}