<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;
use app\helpers\DbHelper;
use yii\data\BaseDataProvider;

abstract class Search extends Model
{
    abstract public function search(array $params): BaseDataProvider;
    abstract protected function getTableName(): string;

    /**
     * @param  string $field
     * @return string
     */
    protected function getField(string $field): string
    {
        return DbHelper::getField($this->getTableName(), $field);
    }
}
