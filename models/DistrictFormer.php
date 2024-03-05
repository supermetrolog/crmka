<?php

namespace app\models;

use app\models\ActiveQuery\DistrictFormerQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_districts_former".
 *
 * @property int $id
 * @property string $title
 * @property int $district_type
 * @property int $deleted
 * @property int $description
 * @property int $order_row
 * @property int $publ_time
 * @property int $last_update
 */
class DistrictFormer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_districts_former';
    }

    /**
     * @return Connection the database connection used by this AR class.
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
            [['title', 'district_type', 'description', 'order_row', 'publ_time', 'last_update'], 'required'],
            [['district_type', 'deleted', 'description', 'order_row', 'publ_time', 'last_update'], 'integer'],
            [['title'], 'string', 'max' => 200],
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
            'district_type' => 'District Type',
            'deleted' => 'Deleted',
            'description' => 'Description',
            'order_row' => 'Order Row',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
        ];
    }

    /**
     * @return DistrictFormerQuery the active query used by this AR class.
     */
    public static function find(): DistrictFormerQuery
    {
        return new DistrictFormerQuery(get_called_class());
    }
}
