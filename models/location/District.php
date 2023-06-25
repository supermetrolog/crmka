<?php

namespace app\models\location;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_districts".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $title_eng
 * @property int|null $district_type
 * @property int|null $exclude
 * @property int|null $deleted
 * @property int|null $order_row
 * @property int|null $publ_time
 * @property int|null $last_update
 * @property int|null $description
 */
class District extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_districts';
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
            [['district_type', 'exclude', 'deleted', 'order_row', 'publ_time', 'last_update', 'description'], 'integer'],
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
            'district_type' => 'District Type',
            'exclude' => 'Exclude',
            'deleted' => 'Deleted',
            'order_row' => 'Order Row',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
            'description' => 'Description',
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
