<?php

declare(strict_types=1);

namespace app\resources\ChatMember;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMember;
use app\models\CommercialOffer;
use app\models\Objects;
use app\models\OfferMix;
use app\models\Request;
use app\models\User;
use app\resources\ActiveRecordResource;
use app\resources\Object\ObjectResource;
use app\resources\OfferMixResource;
use app\resources\Request\RequestResource;
use app\resources\UserResource;
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
			'messages'   => ChatMemberMessageResource::collection($this->resource->messages)
		];
	}

	private function getModel(): JsonResource
	{
		$model = $this->resource->model;

		if ($model instanceof Request) {
			return new RequestResource($model);
		}

		if ($model instanceof Objects) {
			return new ObjectResource($model);
		}

//		if ($model instanceof CommercialOffer) {
//			return new ActiveRecordResource($model);
//		}

		if ($model instanceof User) {
			return new UserResource($model);
		}
//
//		if ($model instanceof OfferMix) {
//			return new OfferMixResource($model);
//		}

		throw new UnexpectedValueException('Unknown model type');
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