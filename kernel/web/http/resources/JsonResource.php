<?php

declare(strict_types=1);

namespace app\kernel\web\http\resources;

use app\helpers\ArrayHelper;
use app\models\ChatMember;
use yii\data\ActiveDataProvider;

abstract class JsonResource
{
	abstract public function toArray(): array;

	/**
	 * @return static
	 */
	public static function make(...$argv): self
	{
		return new static(...$argv);
	}

	/**
	 * @return static
	 */
	public static function tryMake(...$argv): ?self
	{
		$resource = $argv[0];

		if ($resource === null) {
			return null;
		}

		return new static(...$argv);
	}

	/**
	 * @param mixed ...$argv
	 *
	 * @return array|null
	 */
	public static function tryMakeArray(...$argv): ?array
	{
		$resource = $argv[0];

		if ($resource === null) {
			return null;
		}

		return (new static(...$argv))->toArray();
	}

	/**
	 * @param array $resources
	 *
	 * @return array
	 */
	public static function collection(array $resources): array
	{
		return ArrayHelper::map($resources, function ($resource) {
			return static::make($resource)->toArray();
		});
	}

	public static function fromDataProvider(ActiveDataProvider $dataProvider): ActiveDataProvider
	{
		$models = ArrayHelper::map($dataProvider->getModels(), function ($model) {
			return static::make($model)->toArray();
		});

		$dataProvider->setModels($models);

		return $dataProvider;
	}
}