<?php

declare(strict_types=1);

namespace app\resources\Company\Group;

use app\kernel\web\http\resources\JsonResource;
use app\models\Companygroup;

class CompanyGroupResource extends JsonResource
{
	private Companygroup $resource;

	public function __construct(Companygroup $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                 => $this->resource->id,
			'nameRu'             => $this->resource->nameRu,
			'nameEng'            => $this->resource->nameEng,
			'formOfOrganization' => $this->resource->formOfOrganization,
			'description'        => $this->resource->description,
			'full_name'          => $this->resource->getFullName()
		];
	}
}