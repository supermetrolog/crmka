<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Task;
use app\models\User;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\User\UserResource;
use UnexpectedValueException;

class TaskResource extends JsonResource
{
	private Task $resource;

	public function __construct(Task $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'              => $this->resource->id,
			'user_id'         => $this->resource->user_id,
			'message'         => $this->resource->message,
			'status'          => $this->resource->status,
			'start'           => $this->resource->start,
			'end'             => $this->resource->end,
			'created_by_type' => $this->resource->created_by_type,
			'created_by_id'   => $this->resource->created_by_id,
			'created_at'      => $this->resource->created_at,
			'updated_at'      => $this->resource->updated_at,
			'deleted_at'      => $this->resource->deleted_at,
			'user'            => UserShortResource::make($this->resource->user)->toArray(),
			'created_by'      => $this->getCreatedBy()->toArray(),
		];
	}

	private function getCreatedBy(): JsonResource
	{
		$createdBy = $this->resource->createdBy;

		if ($createdBy instanceof User) {
			return new UserResource($createdBy);
		}

		throw new UnexpectedValueException('Unknown created by type');
	}
}