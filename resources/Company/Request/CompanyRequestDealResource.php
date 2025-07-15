<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Deal;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Company\CompanyShortResource;
use app\resources\Deal\DealBaseResource;

class CompanyRequestDealResource extends JsonResource
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
				'offer'      => $this->resource->offer
			]
		);
	}
}