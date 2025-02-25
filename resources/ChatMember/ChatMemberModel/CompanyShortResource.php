<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

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
			'nameEng'              => $this->resource->nameEng,
			'nameRu'               => $this->resource->nameRu,
			'full_name'            => $this->resource->getFullName(),
			'noName'               => $this->resource->noName,
			'companyGroup_id'      => $this->resource->companyGroup_id,
			'activityGroup'        => $this->resource->activityGroup,
			'activityProfile'      => $this->resource->activityProfile,
			'is_individual'        => $this->resource->is_individual,
			'individual_full_name' => $this->resource->individual_full_name,
			'status'               => $this->resource->status
		];
	}
}