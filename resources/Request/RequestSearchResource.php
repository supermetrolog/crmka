<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Request;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Contact\ContactShortResource;

class RequestSearchResource extends JsonResource
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
				'company'           => RequestSearchCompanyResource::tryMakeArray($this->resource->company),
				'contact'           => ContactShortResource::tryMakeArray($this->resource->contact),
				'consultant'        => UserShortResource::tryMakeArray($this->resource->consultant),
				'timeline_progress' => $this->resource->getTimelineProgress()
			]
		);
	}
}