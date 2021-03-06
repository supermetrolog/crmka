<?php

namespace app\models\miniModels;

use Yii;
use app\models\Request;
use app\exceptions\ValidationErrorHttpException;
use ReflectionClass;

/**
 * This is the model class for table "request_direction".
 *
 * @property int $id
 * @property int $request_id
 * @property int $direction
 *
 * @property Request $request
 */
class RequestDirection extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'direction';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'direction'], 'required'],
            [['request_id', 'direction'], 'integer'],
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
            'direction' => 'Direction',
        ];
    }
    public function fields()
    {
        $fields = parent::fields();
        return $fields;
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
