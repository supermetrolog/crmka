<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_add_comment_column_in_timeline_step_object}}`.
 */
class m211021_124955_drop_and_add_comment_column_in_timeline_step_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('timeline_step_object', 'comment');
        $this->addColumn('timeline_step_object', 'comment', $this->text()->defaultValue(null)->comment('комментарий к объекту'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
