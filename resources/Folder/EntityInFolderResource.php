<?php

declare(strict_types=1);

namespace app\resources\Folder;

use app\kernel\web\http\resources\JsonResource;
use app\models\FolderEntity;

class EntityInFolderResource extends JsonResource
{
	private FolderEntity $resource;

	public function __construct(FolderEntity $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'folder_id' => $this->resource->folder_id,
			'entity_id' => $this->resource->entity_id
		];
	}
}