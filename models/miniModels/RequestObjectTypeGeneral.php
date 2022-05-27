<?php

namespace app\models\miniModels;

use Yii;
use app\models\Request;

/**
 * This is the model class for table "request_object_type_general".
 *
 * @property int $id
 * @property int $request_id [СВЯЗЬ] с запросом
 * @property int $type Тип объекта (0 - склад, 1 - производство, 2 - участок
 *
 * @property Request $request
 */
class RequestObjectTypeGeneral extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_object_type_general';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'type'], 'required'],
            [['request_id', 'type'], 'integer'],
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
            'type' => 'Type',
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
