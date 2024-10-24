<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Request;
use app\resources\ChatMember\ChatMemberModel\CompanyShortResource;

class RequestInChatMemberResource extends JsonResource
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
				'company' => CompanyShortResource::tryMakeArray($this->resource->company),
			]
		);
	}
}