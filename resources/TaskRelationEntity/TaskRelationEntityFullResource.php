<?php

declare(strict_types=1);

namespace app\resources\TaskRelationEntity;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Company;
use app\models\Contact;
use app\models\Objects;
use app\models\OfferMix;
use app\models\Request;
use app\models\Survey;
use app\models\Task;
use app\models\TaskRelationEntity;
use app\resources\Survey\SurveyResource;
use app\resources\Task\TaskResource;

class TaskRelationEntityFullResource extends JsonResource
{
	private TaskRelationEntity $resource;

	public function __construct(TaskRelationEntity $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(TaskRelationEntityResource::make($this->resource)->toArray(),
			[
				'entity' => $this->getEntity()->toArray()
			]
		);
	}

	private function getEntity()
	{
		$entity = $this->resource->entity;

		if ($entity instanceof Company) {
			return new TaskRelationEntityCompanyResource($entity);
		}

		if ($entity instanceof Request) {
			return new TaskRelationEntityRequestResource($entity);
		}

		if ($entity instanceof Contact) {
			return new TaskRelationEntityContactResource($entity);
		}

		if ($entity instanceof Task) {
			return new TaskResource($entity);
		}

		if ($entity instanceof Survey) {
			return new SurveyResource($entity);
		}

		if ($entity instanceof OfferMix) {
			return new TaskRelationEntityOfferMixResource($entity);
		}

		if ($entity instanceof Objects) {
			return new TaskRelationEntityObjectResource($entity);
		}

		return $this->resource->entity;
	}
}