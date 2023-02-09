<?php

use app\models\miniModels\TimelineStepObjectComment;
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m220825_150111_update_timeline_id_in_timeline_step_object_comment_from_null_to_timeline_id
 */
class m220825_150111_update_timeline_id_in_timeline_step_object_comment_from_null_to_timeline_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $models = TimelineStepObjectComment::find()->where(['is', 'timeline_id', new Expression('null')])->all();
        if (!$models) {
            return;
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            foreach ($models as $model) {
                $model->timeline_id = $model->timelineStep->timeline_id;
                if (!$model->save(false)) {
                    throw new Exception("SaveModelError");
                }
            }

            $transaction->commit();
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220825_150111_update_timeline_id_in_timeline_step_object_comment_from_null_to_timeline_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220825_150111_update_timeline_id_in_timeline_step_object_comment_from_null_to_timeline_id cannot be reverted.\n";

        return false;
    }
    */
}
