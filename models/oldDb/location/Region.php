<?php

namespace app\models\oldDb\location;

use Yii;

/**
 * This is the model class for table "l_regions".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $title_eng
 * @property string|null $order_desc
 * @property int $cian_id
 * @property int $order_row
 * @property int|null $exclude
 * @property int|null $deleted
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'l_regions';
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
            [['cian_id', 'order_row', 'exclude', 'deleted'], 'integer'],
            [['order_row'], 'required'],
            [['title', 'title_eng', 'order_desc'], 'string', 'max' => 100],
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
            'order_desc' => 'Order Desc',
            'cian_id' => 'Cian ID',
            'order_row' => 'Order Row',
            'exclude' => 'Exclude',
            'deleted' => 'Deleted',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['title'] = function ($fields) {
            return mb_strtolower($fields['title']);
        };

        return $fields;
    }
}
