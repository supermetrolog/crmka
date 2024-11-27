<?php

declare(strict_types=1);

namespace app\resources\Company;

use app\kernel\web\http\resources\JsonResource;
use app\models\Company;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Company\Category\CompanyCategoryResource;
use app\resources\Company\Contact\CompanyContactResource;
use app\resources\Company\Group\CompanyGroupResource;
use app\resources\Company\Object\CompanyObjectResource;
use app\resources\Company\ProductRange\CompanyProductRangeResource;
use app\resources\Company\Request\CompanyRequestResource;

class CompanyInListResource extends JsonResource
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
			'created_at'           => $this->resource->created_at,
			'updated_at'           => $this->resource->updated_at,
			'logo'                 => $this->resource->getLogoUrl(),

			'consultant'    => UserShortResource::tryMakeArray($this->resource->consultant),
			'mainContact'   => CompanyContactResource::tryMakeArray($this->resource->mainContact),
			'categories'    => CompanyCategoryResource::collection($this->resource->categories),
			'productRanges' => CompanyProductRangeResource::collection($this->resource->productRanges),
			'companyGroup'  => CompanyGroupResource::tryMakeArray($this->resource->companyGroup),

			'objects'  => CompanyObjectResource::collection($this->resource->objects),
			'requests' => CompanyRequestResource::collection($this->resource->requests),

			'objects_count'  => $this->resource->objects_count,
			'requests_count' => $this->resource->requests_count,
			'contacts_count' => $this->resource->contacts_count
		];
	}
}