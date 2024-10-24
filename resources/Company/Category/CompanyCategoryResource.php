<?php

declare(strict_types=1);

namespace app\resources\Company\Category;

use app\kernel\web\http\resources\JsonResource;
use app\models\Category;

class CompanyCategoryResource extends JsonResource
{
	private Category $resource;

	public function __construct(Category $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'       => $this->resource->id,
			'category' => $this->resource->category
		];
	}
}