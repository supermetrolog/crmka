<?php

namespace app\resources\Attribute;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\views\AttributeSearchView;

class AttributeSearchResource extends JsonResource
{
	private AttributeSearchView $resource;

	public function __construct(AttributeSearchView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			AttributeResource::make($this->resource)->toArray(),
			[
				'options_count' => $this->resource->options_count,
			],
		);
	}
}