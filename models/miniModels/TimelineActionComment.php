<?php

namespace app\models\miniModels;

use app\exceptions\ValidationErrorHttpException;
use IntlDateFormatter;
use Yii;

/**
 * This is the model class for table "timeline_action_comment".
 *
 * @property int $id
 * @property int $timeline_step_id [связь]
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
            [['timeline_step_id'], 'integer'],
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
        ];
    }
    public static function addActionComments($post_data)
    {
        $newActions = $post_data['newActionComments'];
        foreach ($newActions as $action) {
            $model = new TimelineActionComment();
            $action['comment'] = trim($action['comment']);
            if (!$model->load($action, '') || !$model->save()) {
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
        }
    }
    public function fields()
    {
        $fields = parent::fields();

        $fields['created_at'] = function ($fields) {
            return Yii::$app->formatter->format($fields['created_at'], 'datetime');
        };
        return $fields;
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
}
