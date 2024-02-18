<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $title
 */
class ElevatorType extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'l_elevators_types';
    }

	/**
	 * @throws InvalidConfigException
	 */
	public static function getDb()
    {
        return Yii::$app->get('db_old');
    }

    public function rules(): array
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }
}
