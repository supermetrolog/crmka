<?php

use yii\db\Migration;

/**
 * Class m220204_125839_add_timeline_id_column_in_timeline_action_comment_table
 */
class m220204_125839_add_timeline_id_column_in_timeline_action_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_action_comment', 'timeline_id', $this->integer()->defaultValue(null)->comment('[СВЯЗЬ]'));
        $this->addForeignKey('fk-timeline_action_comment-timeline_id', 'timeline_action_comment', 'timeline_id', 'timeline', 'id', 'CASCADE');
        $this->createIndex('idx-timeline_action_comment-timeline_id', 'timeline_action_comment', 'timeline_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220204_125839_add_timeline_id_column_in_timeline_action_comment_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220204_125839_add_timeline_id_column_in_timeline_action_comment_table cannot be reverted.\n";

        return false;
    }
    */
}
