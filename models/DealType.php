<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_deal_types".
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $exclude
 * @property int|null $cian_id
 * @property int|null $deleted
 */
class DealType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_deal_types';
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
            [['exclude', 'cian_id', 'deleted'], 'integer'],
            [['title'], 'string', 'max' => 20],
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
            'exclude' => 'Exclude',
            'cian_id' => 'Cian ID',
            'deleted' => 'Deleted',
        ];
    }

    public function fields()
    {
        $f = parent::fields();

        unset(
            $f['exclude'],
            $f['cian_id'],
            $f['deleted'],
        );
        return $f;
    }
}
