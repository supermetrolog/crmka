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
			'id'              => $this->resource->id,
			'nameEng'         => $this->resource->nameEng,
			'nameRu'          => $this->resource->nameRu,
			'noName'          => $this->resource->noName,
			'activityGroup'   => $this->resource->activityGroup,
			'activityProfile' => $this->resource->activityProfile,
		];
	}
}