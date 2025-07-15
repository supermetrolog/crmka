<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Request;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Request\RequestFullResource;

class CompanyRequestFullResource extends JsonResource
{
	private Request $resource;

	public function __construct(Request $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			RequestFullResource::make($this->resource)->toArray(),
			[
				'consultant' => UserShortResource::tryMakeArray($this->resource->consultant),
				'timeline'   => CompanyRequestTimelineResource::tryMakeArray($this->resource->mainTimeline),
				'deal'       => CompanyRequestDealResource::tryMakeArray($this->resource->deal)
			]
		);
	}
}