<?php

declare(strict_types=1);

namespace app\resources\Company\Survey;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Survey;
use app\resources\Call\CallResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\ChatMemberMessage\ChatMemberMessageInlineResource;
use app\resources\Contact\ContactResource;
use app\resources\Survey\SurveyBaseResource;

class CompanySurveyResource extends JsonResource
{
	private Survey $resource;

	public function __construct(Survey $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			SurveyBaseResource::make($this->resource)->toArray(),
			[
				'user'    => UserShortResource::make($this->resource->user)->toArray(),
				'contact' => ContactResource::tryMakeArray($this->resource->contact),
				'calls'   => CallResource::collection($this->resource->calls),
				'message' => ChatMemberMessageInlineResource::tryMakeArray($this->resource->chatMemberMessage)
			]
		);
	}
}