<?php

declare(strict_types=1);

namespace app\resources\Deal;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Deal;
use app\resources\ChatMember\ChatMemberModel\RequestShortResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Company\CompanyShortResource;

class DealSearchResource extends JsonResource
{
	private Deal $resource;

	public function __construct(Deal $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			DealBaseResource::makeArray($this->resource),
			[
				'company'    => CompanyShortResource::tryMakeArray($this->resource->company),
				'competitor' => CompanyShortResource::tryMakeArray($this->resource->competitor),
				'consultant' => UserShortResource::tryMakeArray($this->resource->consultant),
				'request'    => RequestShortResource::tryMakeArray($this->resource->request),
				'offerMix'   => $this->resource->offer
			]
		);
	}
}