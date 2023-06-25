<?php

namespace app\models\location;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_metros".
 *
 * @property int $id
 * @property string $title
 * @property string $title_eng
 * @property int|null $exclude
 * @property int|null $deleted
 */
class Metro extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_metros';
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
            [['title', 'title_eng'], 'string', 'max' => 200],
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

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        return [
            'id' => $fields['id'],
            'title' => $fields['title'],
            'title_eng' => $fields['title_eng'],
            'exclude' => $fields['exclude'],
            'deleted' => $fields['deleted'],
        ];
    }
}
