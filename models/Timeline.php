<?php

namespace app\models;

use app\models\miniModels\TimelineAction;
use Yii;

/**
 * This is the model class for table "timeline".
 *
 * @property int $id
 * @property int $request_id
 * @property int $step
 * @property int $isBranch
 * @property int $branch
 * @property string|null $datetime
 *
 * @property Request $request
 * @property TimelineAction[] $timelineActions
 */
class Timeline extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'step', 'isBranch', 'branch'], 'required'],
            [['request_id', 'step', 'isBranch', 'branch'], 'integer'],
            [['datetime'], 'safe'],
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
            'step' => 'Step',
            'isBranch' => 'Is Branch',
            'branch' => 'Branch',
            'datetime' => 'Datetime',
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

    /**
     * Gets query for [[TimelineActions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineActions()
    {
        return $this->hasMany(TimelineAction::className(), ['timeline_id' => 'id']);
    }
}
