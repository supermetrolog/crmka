<?php

declare(strict_types=1);

namespace app\resources\Company\File;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\CompanyFile;
use yii\base\InvalidConfigException;

class CompanyFileResource extends JsonResource
{
	private CompanyFile $resource;

	public function __construct(CompanyFile $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @throws InvalidConfigException
	 */
	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'name'       => $this->resource->name,
			'filename'   => $this->resource->filename,
			'size'       => $this->resource->size,
			'type'       => $this->resource->type,
			'src'        => $this->resource->getSrc(),
			'created_at' => $this->resource->created_at,

		];
	}
}