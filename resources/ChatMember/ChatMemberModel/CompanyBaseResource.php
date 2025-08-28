<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\Company\Company;
use app\resources\Company\ActivityGroup\CompanyActivityGroupResource;
use app\resources\Company\ActivityProfile\CompanyActivityProfileResource;
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
			'consultant_id'        => $this->resource->consultant_id,
			'consultant'           => UserShortResource::tryMakeArray($this->resource->consultant),
			'full_name'            => $this->resource->getFullName(),
			'logo'                 => $this->resource->getLogoUrl(),
			'categories'           => CompanyCategoryResource::collection($this->resource->categories),
			'companyGroup_id'      => $this->resource->companyGroup_id,
			'companyGroup'         => CompanyGroupResource::tryMakeArray($this->resource->companyGroup),
			'office_address'       => $this->resource->officeAdress,
			'legal_address'        => $this->resource->legalAddress,
			'status'               => $this->resource->status,
			'show_product_ranges'  => $this->resource->show_product_ranges,
			'created_at'           => $this->resource->created_at,
			'updated_at'           => $this->resource->updated_at,

			'objects_count'         => $this->resource->objectsCount,
			'requests_count'        => $this->resource->requestsCount,
			'active_requests_count' => $this->resource->activeRequestsCount,
			'contacts_count'        => $this->resource->contactsCount,
			'active_contacts_count' => $this->resource->activeContactsCount,

			'activity_groups'   => CompanyActivityGroupResource::collection($this->resource->companyActivityGroups),
			'activity_profiles' => CompanyActivityProfileResource::collection($this->resource->companyActivityProfiles),
		];
	}
}