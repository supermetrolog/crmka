<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $sign
 * @property string $color
 * @property string $order_row
 */
class FloorNumber extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_floor_nums';
    }

    /**
     * @return Connection
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('db_old');
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'description', 'sign', 'color', 'order_row'], 'required'],
            [['title', 'description'], 'string', 'max' => 100],
            [['sign'], 'string', 'max' => 5],
            [['color', 'order_row'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'sign' => 'Sign',
            'color' => 'Color',
            'order_row' => 'Order Row',
        ];
    }
}
