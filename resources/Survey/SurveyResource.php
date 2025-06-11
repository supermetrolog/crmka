<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Survey;
use app\resources\Call\CallShortResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\ChatMember\ChatMemberShortResource;
use app\resources\Contact\ContactResource;

class SurveyResource extends JsonResource
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
				'user'       => UserShortResource::make($this->resource->user)->toArray(),
				'contact'    => ContactResource::tryMakeArray($this->resource->contact),
				'chatMember' => ChatMemberShortResource::tryMakeArray($this->resource->chatMember),
				'calls'      => CallShortResource::collection($this->resource->calls)
			]
		);
	}
}