<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\views\RequestSearchView;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Contact\ContactShortResource;

class RequestSearchResource extends JsonResource
{
	private RequestSearchView $resource;

	public function __construct(RequestSearchView $resource)
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
				'timeline_progress' => $this->resource->getTimelineProgress(),
				'tasks_count'       => $this->resource->tasks_count,
				'has_survey_draft'  => $this->resource->has_survey_draft,
			]
		);
	}
}