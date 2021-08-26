<?php

namespace app\models\miniModels;

use Yii;
use app\models\Request;

/**
 * This is the model class for table "request_object_class".
 *
 * @property int $id
 * @property int $request_id
 * @property int $object_class
 *
 * @property Request $request
 */
class RequestObjectClass extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'object_class';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_object_class';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'object_class'], 'required'],
            [['request_id', 'object_class'], 'integer'],
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
            'object_class' => 'Object Class',
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
