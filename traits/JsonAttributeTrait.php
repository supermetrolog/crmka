<?php

namespace app\traits;

use app\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 *
 */
trait JsonAttributeTrait
{
	private array $__jsonCache = [];

	protected function getJson(string $attr): ?array
	{
		if (ArrayHelper::keyExists($this->__jsonCache, $attr)) {
			return $this->__jsonCache[$attr];
		}

		$raw = $this->$attr;

		if ($raw === null || $raw === '') {
			return $this->__jsonCache[$attr] = null;
		}

		return $this->__jsonCache[$attr] = Json::decode($raw);
	}

	/**
	 * @param array|string|null $value
	 */
	protected function setJson(string $attr, $value): void
	{
		if (ArrayHelper::isArray($value)) {
			$this->__jsonCache[$attr] = $value;
			$this->$attr              = Json::encode($value);

			return;
		}

		if ($value === null || $value === '') {
			$this->__jsonCache[$attr] = null;
			$this->$attr              = null;

			return;
		}

		$decoded = Json::decode((string)$value);

		$this->__jsonCache[$attr] = $decoded;
		$this->$attr              = Json::encode($decoded);
	}

	public function afterFind(): void
	{
		parent::afterFind();
		$this->__jsonCache = [];
	}
}