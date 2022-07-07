<?php

namespace app\models;

use app\exceptions\ValidationErrorHttpException;
use app\models\Request;
use Yii;

/**
 * This is the model class for table "service_selection".
 *
 * @property int $id
 * @property int $request_id [СВЯЗЬ] с запросом
 * @property int|null $recommended_offers_count колличество предложений в последей подборке
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Request $request
 */
class ServiceSelection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_selection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id'], 'required'],
            [['request_id', 'recommended_offers_count'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
            'recommended_offers_count' => 'Recommended Offers Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public static function createSelection($data)
    {
        $model = new self();

        if ($model->load($data, '') && $model->save()) {
            return true;
        }
        throw new ValidationErrorHttpException($model->getErrorSummary(false));
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
