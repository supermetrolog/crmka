<?php

use yii\db\Migration;

/**
 * Class m220825_135704_add_timeline_id_culumn_in_timeline_object_comment_table
 */
class m220825_135704_add_timeline_id_culumn_in_timeline_object_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("timeline_step_object_comment", 'timeline_id', $this->integer()->comment("[СВЯЗЬ] с таймлайном"));
        $this->createIndex(
            'idx-timeline_step_object_comment-timeline_id',
            'timeline_step_object_comment',
            'timeline_id'
        );

        $this->addForeignKey(
            'fk-timeline_step_object_comment-timeline_id',
            'timeline_step_object_comment',
            'timeline_id',
            'timeline',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220825_135704_add_timeline_id_culumn_in_timeline_object_comment_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220825_135704_add_timeline_id_culumn_in_timeline_object_comment_table cannot be reverted.\n";

        return false;
    }
    */
}
