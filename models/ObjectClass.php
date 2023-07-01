<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * @property int $id
 * @property string|null $title
 * @property string $title_short1
 * @property string $title_short
 * @property string $description
 * @property int $order_row
 * @property int $publ_time
 * @property int $activity
 * @property int|null $exclude
 */
class ObjectClass extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_classes';
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
            [['title_short1', 'title_short', 'description', 'order_row', 'publ_time'], 'required'],
            [['description'], 'string'],
            [['order_row', 'publ_time', 'activity', 'exclude'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['title_short1', 'title_short'], 'string', 'max' => 5],
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
            'title_short1' => 'Title Short1',
            'title_short' => 'Title Short',
            'description' => 'Description',
            'order_row' => 'Order Row',
            'publ_time' => 'Publ Time',
            'activity' => 'Activity',
            'exclude' => 'Exclude',
        ];
    }
}
