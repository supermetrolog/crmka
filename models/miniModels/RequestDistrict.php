<?php

namespace app\models\miniModels;

use app\models\Request;
use app\exceptions\ValidationErrorHttpException;
use ReflectionClass;
use Yii;

/**
 * This is the model class for table "request_district".
 *
 * @property int $id
 * @property int $request_id
 * @property int $district
 *
 * @property Request $request
 */
class RequestDistrict extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'district';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_district';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'district'], 'required'],
            [['request_id', 'district'], 'integer'],
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
            'district' => 'District',
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
