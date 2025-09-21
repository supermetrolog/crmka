<?php

declare(strict_types=1);

namespace app\resources\ChatMember;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMember;
use app\models\Company\Company;
use app\models\ObjectChatMember;
use app\models\Request;
use app\models\User\User;
use app\resources\Call\CallResource;
use app\resources\ChatMember\ChatMemberModel\CompanyBaseResource;
use app\resources\Object\ObjectChatMemberResource;
use app\resources\Request\RequestInChatMemberResource;
use app\resources\User\UserResource;
use UnexpectedValueException;

class ChatMemberFullResource extends JsonResource
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
			'last_call'  => CallResource::tryMakeArray($this->resource->lastCall),
			//			'messages'   => ChatMemberMessageResource::collection($this->resource->messages)
		];
	}

	private function getModel(): JsonResource
	{
		$model = $this->resource->model;

		if ($model instanceof Request) {
			return new RequestInChatMemberResource($model);
		}

		if ($model instanceof ObjectChatMember) {
			return new ObjectChatMemberResource($model);
		}

		if ($model instanceof User) {
			return new UserResource($model);
		}

		if ($model instanceof Company) {
			return new CompanyBaseResource($model);
		}

		throw new UnexpectedValueException('Unknown model type');
	}
}