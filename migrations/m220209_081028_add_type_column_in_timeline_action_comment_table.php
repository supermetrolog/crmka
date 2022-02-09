<?php

use yii\db\Migration;

/**
 * Class m220209_081028_add_type_column_in_timeline_action_comment_table
 */
class m220209_081028_add_type_column_in_timeline_action_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_action_comment', 'type', $this->tinyInteger()->notNull()->defaultValue(0)->comment('Тип коммента (коммент к объекту, просто коммент и т.д'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220209_081028_add_type_column_in_timeline_action_comment_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220209_081028_add_type_column_in_timeline_action_comment_table cannot be reverted.\n";

        return false;
    }
    */
}
