<?php

namespace app\models\miniModels;

use Yii;
use app\models\Request;
use app\models\User;
use app\exceptions\ValidationErrorHttpException;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "request_deal".
 *
 * @property int $id
 * @property int $request_id [связб] с запросом
 * @property int $consultant_id [связь] с юзером
 * @property int|null $area площадь сделки
 * @property int|null $floorPrice цена пола
 * @property string|null $clientLegalEntity юридическое лицо клиента в сделке
 * @property string|null $description описание сделки
 * @property string|null $startEventTime время начала события
 * @property string|null $endEventTime время завершения события
 * @property string|null $created_at
 * @property string|null $name название сделки
 *
 * @property User $consultant
 * @property Request $request
 */
class RequestDeal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_deal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'consultant_id'], 'required'],
            [['request_id', 'consultant_id', 'area', 'floorPrice', 'object_id', 'complex_id'], 'integer'],
            [['startEventTime', 'endEventTime', 'created_at'], 'safe'],
            [['clientLegalEntity', 'description', 'name'], 'string', 'max' => 255],
            [['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
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
            'consultant_id' => 'Consultant ID',
            'area' => 'Area',
            'floorPrice' => 'Floor Price',
            'clientLegalEntity' => 'Client Legal Entity',
            'description' => 'Description',
            'startEventTime' => 'Start Event Time',
            'endEventTime' => 'End Event Time',
            'created_at' => 'Created At',
            'name' => 'Name',
            'object_id' => 'Object ID',
            'complex_id' => 'Complex ID'
        ];
    }
    public static function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public static function createDeal($data)
    {
        $db = Yii::$app->db;
        $deal = new RequestDeal();
        $transaction = $db->beginTransaction();
        try {
            if ($deal->load($data, '') && $deal->save()) {
                if (Request::changeStatus($data['request_id'], Request::STATUS_DONE)) {
                    $transaction->commit();
                    return true;
                }
            }
            throw new ValidationErrorHttpException($deal->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public static function updateDeal($data)
    {
        $db = Yii::$app->db;
        $deal = self::findModel($data['id']);
        $transaction = $db->beginTransaction();
        try {
            if ($deal->load($data, '') && $deal->save()) {
                $transaction->commit();
                return true;
            }
            throw new ValidationErrorHttpException($deal->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public function fields()
    {
        $fields = parent::fields();
        $fields['startEventTime'] = function ($fields) {
            if ($fields['startEventTime']) {
                return date('Y-m-d', strtotime($fields['startEventTime']));
            }
            return $fields['startEventTime'];
        };
        $fields['endEventTime'] = function ($fields) {
            if ($fields['endEventTime']) {
                return date('Y-m-d', strtotime($fields['endEventTime']));
            }
            return $fields['endEventTime'];
        };
        return $fields;
    }
    /**
     * Gets query for [[Consultant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConsultant()
    {
        return $this->hasOne(User::className(), ['id' => 'consultant_id']);
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
