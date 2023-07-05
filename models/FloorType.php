<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * @property int $id
 * @property string|null $title
 * @property string|null $title_short
 * @property string|null $title_cian
 * @property int|null $exclude
 */
class FloorType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_floor_types';
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
            [['exclude'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['title_short', 'title_cian'], 'string', 'max' => 100],
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
            'title_short' => 'Title Short',
            'title_cian' => 'Title Cian',
            'exclude' => 'Exclude',
        ];
    }
}
