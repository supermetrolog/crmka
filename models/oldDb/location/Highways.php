<?php

namespace app\models\oldDb\location;

use Yii;

/**
 * This is the model class for table "l_highways".
 *
 * @property int $id
 * @property string $title
 * @property string $title_eng
 * @property int|null $exclude
 * @property int|null $deleted
 */
class Highways extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'l_highways';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_old');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
    public function attributeLabels()
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
