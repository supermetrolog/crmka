<?php

namespace app\behaviors;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\models\AR\AR;
use Exception;
use ReflectionClass;
use ReflectionException;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class CreateManyMiniModelsBehaviors extends Behavior
{
	public $owner_id;

	public $relation_column;

	/**
	 * @return void
	 * @throws Exception
	 */
	private function setData(): void
	{
		if (!$this->relation_column) {
			$this->relation_column = $this->owner->tableName() . "_id";
		}

		if (!$this->owner_id) {
			$this->owner_id = $this->owner->id;

			if (!$this->owner_id) {
				throw new Exception('Model id not found');
			}
		}
	}

	/**
	 * @param array $modelsData
	 *
	 * @return bool
	 * @throws ValidationErrorHttpException
	 * @throws ReflectionException
	 * @throws Exception
	 */
	public function createManyMiniModels(array $modelsData): bool
	{
		$this->setData();

		foreach ($modelsData as $className => $data) {
			if (!$data) {
				continue;
			}

			$class = new ReflectionClass($className);

			foreach ($data as $item) {
				$model                        = $class->newInstance();
				$item[$this->relation_column] = $this->owner_id;

				$model->load($item, '');
				$model->saveOrThrow();
			}
		}

		return true;
	}

	/**
	 * @param $modelsData
	 *
	 * @return void
	 * @throws ReflectionException
	 * @throws ValidationErrorHttpException
	 * @throws Exception
	 */
	public function updateManyMiniModels($modelsData): void
	{
		$this->setData();

		/**
		 * @var AR|ActiveRecord $className
		 * @var                 $item
		 */
		foreach ($modelsData as $className => $item) {
			$className::deleteAll([$this->relation_column => $this->owner_id]);
		}

		$this->createManyMiniModels($modelsData);
	}
}
