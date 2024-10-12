<?php

declare(strict_types=1);

namespace app\resources\ChatMember;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMember;
use app\models\ObjectChatMember;
use app\models\Request;
use app\models\User;
use app\resources\Call\CallResource;
use app\resources\ChatMember\ChatMemberModel\ObjectChatMemberShortResource;
use app\resources\ChatMember\ChatMemberModel\RequestShortResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use UnexpectedValueException;
use yii\data\ActiveDataProvider;

class ChatMemberResource extends JsonResource
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
			'statistic'  => $this->getStatistic(),
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

		throw new UnexpectedValueException('Unknown model type');
	}

	private function getStatistic(): array
	{
		if (!$this->resource->is_linked) {
			return [
				'tasks'         => 0,
				'notifications' => 0,
				'messages'      => 0,
			];
		}

		return [
			'tasks'         => $this->resource->unread_task_count,
			'notifications' => $this->resource->unread_notification_count,
			'messages'      => $this->resource->unread_message_count,
		];
	}

	public static function fromDataProvider(ActiveDataProvider $dataProvider): ActiveDataProvider
	{
		$dataProvider->setModels(array_map(
			function (ChatMember $request) {
				return (new self($request))->toArray();
			},
			$dataProvider->getModels()
		));

		return $dataProvider;
	}
}