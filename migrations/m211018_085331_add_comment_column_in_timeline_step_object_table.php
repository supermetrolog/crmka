<?php

use yii\db\Migration;

/**
 * Class m211018_085331_add_comment_column_in_timeline_step_object_table
 */
class m211018_085331_add_comment_column_in_timeline_step_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_step_object', 'comment', $this->string()->defaultValue(null)->comment('комментарий к объекту'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211018_085331_add_comment_column_in_timeline_step_object_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211018_085331_add_comment_column_in_timeline_step_object_table cannot be reverted.\n";

        return false;
    }
    */
}
