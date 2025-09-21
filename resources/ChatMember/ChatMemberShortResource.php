<?php

declare(strict_types=1);

namespace app\resources\ChatMember;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMember;
use app\models\Company\Company;
use app\models\ObjectChatMember;
use app\models\Request;
use app\models\User\User;
use app\resources\ChatMember\ChatMemberModel\CompanyBaseResource;
use app\resources\ChatMember\ChatMemberModel\ObjectChatMemberShortResource;
use app\resources\ChatMember\ChatMemberModel\RequestShortResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use UnexpectedValueException;

class ChatMemberShortResource extends JsonResource
{
	private ChatMember $resource;

	public function __construct(ChatMember $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'model_type' => $this->resource->model_type,
			'model_id'   => $this->resource->model_id,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'model'      => $this->getModel()->toArray(),
		];
	}

	private function getModel(): JsonResource
	{
		$model = $this->resource->model;

		if ($model instanceof Request) {
			return new RequestShortResource($model);
		}

		if ($model instanceof ObjectChatMember) {
			return new ObjectChatMemberShortResource($model);
		}

		if ($model instanceof User) {
			return new UserShortResource($model);
		}

		if ($model instanceof Company) {
			return new CompanyBaseResource($model);
		}

		throw new UnexpectedValueException('Unknown model type');
	}
}