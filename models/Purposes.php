<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;

/**
 * @property int $id
 * @property string $title
 * @property string $title_eng
 * @property string $title_short
 * @property string $title_cian
 * @property string $icon
 * @property string $description
 * @property int $order_row
 * @property int $publ_time
 * @property int $activity
 * @property int|null $exclude
 * @property int|null $type
 */
class Purposes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_purposes';
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
            [['title', 'title_eng', 'title_short', 'title_cian', 'icon', 'description', 'order_row', 'publ_time', 'activity'], 'required'],
            [['description'], 'string'],
            [['order_row', 'publ_time', 'activity', 'exclude', 'type'], 'integer'],
            [['title', 'title_eng', 'title_short', 'title_cian'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 50],
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
            'title_eng' => 'Title Eng',
            'title_short' => 'Title Short',
            'title_cian' => 'Title Cian',
            'icon' => 'Icon',
            'description' => 'Description',
            'order_row' => 'Order Row',
            'publ_time' => 'Publ Time',
            'activity' => 'Activity',
            'exclude' => 'Exclude',
            'type' => 'Type',
        ];
    }
}
