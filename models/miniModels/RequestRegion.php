<?php

namespace app\models\miniModels;

use Yii;
use app\models\Request;

/**
 * This is the model class for table "request_region".
 *
 * @property int $id
 * @property int $request_id
 * @property int $region
 *
 * @property Request $request
 */
class RequestRegion extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'region';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'region'], 'required'],
            [['request_id', 'region'], 'integer'],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::className(), 'targetAttribute' => ['request_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_id' => 'Request ID',
            'region' => 'Region',
        ];
    }

    /**
     * Gets query for [[Request]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'request_id']);
    }
}
