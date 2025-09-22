<?php

declare(strict_types=1);

namespace app\components\Integrations;

use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use yii\base\BaseObject;

class IntegrationModel extends BaseObject
{
	protected array $casts = [];

	public function __construct($config = [])
	{
		foreach ($this->casts as $attr => $cast) {
			if (!ArrayHelper::keyExists($config, $attr)) {
				continue;
			}

			$value = $config[$attr];

			if (StringHelper::isString($cast) && ArrayHelper::isArray($value)) {
				if ($this->hasMethod($cast)) {
					$config[$attr] = $this->$cast($value);
				} else {
					$config[$attr] = new $cast($value);
				}

			}

			if (ArrayHelper::isArray($cast) && ArrayHelper::length($cast) === 1 && ArrayHelper::isArray($value)) {
				$class = $cast[0];

				$list = [];

				foreach ($value as $item) {
					$list[] = ArrayHelper::isArray($item) ? new $class($item) : $item;
				}

				$config[$attr] = $list;
			}
		}

		$preparedConfig = ArrayHelper::filter($config, fn($key) => $this->hasProperty($key), ARRAY_FILTER_USE_KEY);

		parent::__construct($preparedConfig);
	}
}
