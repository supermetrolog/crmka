<?php

namespace app\behaviors;

use yii\base\Behavior;
use ReflectionClass;
use app\exceptions\ValidationErrorHttpException;
use Exception;

class CreateManyMiniModelsBehaviors extends Behavior
{
    public $owner_id;

    public $relation_column;
    private function setData()
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
    //функция для создания сразу нескольких строк в связанных моделях
    public  function createManyMiniModels(array $modelsData)
    {
        $this->setData();
        foreach ($modelsData as $className => $data) {
            if (!$data) continue;
            $class = new ReflectionClass($className);
            foreach ($data as $item) {
                $model = $class->newInstance();
                $item[$this->relation_column] = $this->owner_id;
                if (!$model->load($item, '') || !$model->save())
                    throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
        }
        return true;
    }
    public function updateManyMiniModels($modelsData)
    {
        $this->setData();
        foreach ($modelsData as $className => $item) {
            $className::deleteAll([$this->relation_column => $this->owner_id]);
        }
        $this->createManyMiniModels($modelsData);
    }
}
