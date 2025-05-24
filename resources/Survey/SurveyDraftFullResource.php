<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\SurveyDraft;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\ChatMember\ChatMemberShortResource;

class SurveyDraftFullResource extends JsonResource
{
	private SurveyDraft $resource;

	public function __construct(SurveyDraft $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			SurveyDraftBaseResource::make($this->resource)->toArray(),
			[
				'user'       => UserShortResource::make($this->resource->user)->toArray(),
				'chatMember' => ChatMemberShortResource::tryMakeArray($this->resource->chatMember)
			]
		);
	}
}