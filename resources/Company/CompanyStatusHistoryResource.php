<?php

declare(strict_types=1);

namespace app\resources\Company;

use app\kernel\web\http\resources\JsonResource;
use app\models\Company\CompanyStatusHistory;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class CompanyStatusHistoryResource extends JsonResource
{
	private CompanyStatusHistory $resource;

	public function __construct(CompanyStatusHistory $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                => $this->resource->id,
			'company_id'        => $this->resource->company_id,
			'status'            => $this->resource->status,
			'reason'            => $this->resource->reason,
			'created_at'        => $this->resource->created_at,
			'comment'           => $this->resource->comment,
			'changed_by_source' => $this->resource->changed_by_source,
			'changed_by_id'     => $this->resource->changed_by_id,

			'changed_by' => UserShortResource::tryMakeArray($this->resource->changedBy),
		];
	}
}