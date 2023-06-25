<?php

namespace app\models\location;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_districts_moscow".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $title_eng
 * @property string|null $code
 * @property string $title_short
 * @property int|null $exclude
 * @property int|null $deleted
 */
class DistrictMoscow extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_districts_moscow';
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
            [['title_short'], 'required'],
            [['exclude', 'deleted'], 'integer'],
            [['title', 'title_eng'], 'string', 'max' => 20],
            [['code', 'title_short'], 'string', 'max' => 10],
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
            'code' => 'Code',
            'title_short' => 'Title Short',
            'exclude' => 'Exclude',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        return [
            'id' => $fields['id'],
            'title' => $fields['title'],
            'title_short' => $fields['title_short'],
            'title_eng' => $fields['title_eng'],
            'exclude' => $fields['exclude'],
            'deleted' => $fields['deleted'],
        ];
    }
}
