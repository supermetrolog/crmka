<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Task;
use app\models\User;
use yii\db\ActiveRecord;

class ActiveRecordResource extends JsonResource
{
	private ActiveRecord $resource;
	private array        $expand;

	public function __construct(ActiveRecord $resource, array $expand = [])
	{
		$this->resource = $resource;
		$this->expand   = $expand;
	}

	public function toArray(): array
	{
		return $this->resource->toArray([], $this->expand);
	}
}