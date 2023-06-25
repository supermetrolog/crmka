<?php

namespace app\models\location;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_towns_central".
 *
 * @property int $id
 * @property string $title
 * @property string $title_eng
 * @property int|null $exclude
 * @property int|null $deleted
 */
class TownCentral extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_towns_central';
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
            [['title', 'title_eng'], 'required'],
            [['exclude', 'deleted'], 'integer'],
            [['title', 'title_eng'], 'string', 'max' => 100],
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
            'exclude' => 'Exclude',
            'deleted' => 'Deleted',
        ];
    }
}
