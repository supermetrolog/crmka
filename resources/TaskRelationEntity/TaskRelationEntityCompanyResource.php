<?php

declare(strict_types=1);

namespace app\resources\TaskRelationEntity;

use app\kernel\web\http\resources\JsonResource;
use app\models\Company\Company;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Company\ActivityGroup\CompanyActivityGroupResource;
use app\resources\Company\ActivityProfile\CompanyActivityProfileResource;
use app\resources\Company\Category\CompanyCategoryResource;
use app\resources\Company\Group\CompanyGroupResource;

class TaskRelationEntityCompanyResource extends JsonResource
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
			'consultant_id'        => $this->resource->consultant_id,
			'full_name'            => $this->resource->getFullName(),
			'logo'                 => $this->resource->getLogoUrl(),
			'companyGroup_id'      => $this->resource->companyGroup_id,
			'status'               => $this->resource->status,
			'created_at'           => $this->resource->created_at,
			'updated_at'           => $this->resource->updated_at,

			'consultant'        => UserShortResource::tryMakeArray($this->resource->consultant),
			'categories'        => CompanyCategoryResource::collection($this->resource->categories),
			'companyGroup'      => CompanyGroupResource::tryMakeArray($this->resource->companyGroup),
			'activity_groups'   => CompanyActivityGroupResource::collection($this->resource->companyActivityGroups),
			'activity_profiles' => CompanyActivityProfileResource::collection($this->resource->companyActivityProfiles),
		];
	}
}