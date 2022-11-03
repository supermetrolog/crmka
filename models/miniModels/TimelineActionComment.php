<?php

namespace app\models\miniModels;

use app\exceptions\ValidationErrorHttpException;
use app\models\Timeline;
use IntlDateFormatter;
use yii\helpers\ArrayHelper;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "timeline_action_comment".
 *
 * @property int $id
 * @property int $timeline_step_id [связь]
 * @property int $timeline_id [связь]
 * @property int $timeline_step_number номер степа
 * @property int $type тип коммента
 * @property string $comment комментарий к действию
 * @property string $created_at
 * @property string|null $title
 *
 * @property TimelineStep $timelineStep
 */
class TimelineActionComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline_action_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_step_id', 'comment'], 'required'],
            [['timeline_step_id', 'timeline_id', 'timeline_step_number', 'type'], 'integer'],
            [['created_at'], 'safe'],
            [['comment', 'title'], 'string'],
            [['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStep::className(), 'targetAttribute' => ['timeline_step_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timeline_step_id' => 'Timeline Step ID',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'title' => 'Title',
            'type' => 'Type',
        ];
    }
    public function beforeSave($insert)
    {
        $this->comment = trim($this->comment);
        return parent::beforeSave($insert);
    }
    public static function addActionComments($post_data)
    {
        if (!ArrayHelper::keyExists('newActionComments', $post_data)) {
            return true;
        }
        $newActions = $post_data['newActionComments'];
        foreach ($newActions as $action) {
            $model = new TimelineActionComment();
            if ($model->load($action, '') && $model->save()) {
                continue;
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
        return true;
    }
    public function fields()
    {
        $fields = parent::fields();

        $fields['created_at_format'] = function ($fields) {
            return Yii::$app->formatter->format($fields['created_at'], 'date');
        };
        return $fields;
    }

    public static function getTimelineComments($timeline_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->where(['timeline_action_comment.timeline_id' => $timeline_id])->orderBy(['timeline_action_comment.created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 0
            ]
        ]);
        return $dataProvider;
    }
    /**
     * Gets query for [[TimelineStep]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineStep()
    {
        return $this->hasOne(TimelineStep::className(), ['id' => 'timeline_step_id']);
    }
    /**
     * Gets query for [[TimelineStep]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeline()
    {
        return $this->hasOne(Timeline::className(), ['id' => 'timeline_id']);
    }
}
