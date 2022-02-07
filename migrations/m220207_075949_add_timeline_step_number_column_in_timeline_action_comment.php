<?php

use yii\db\Migration;

/**
 * Class m220207_075949_add_timeline_step_number_column_in_timeline_action_comment
 */
class m220207_075949_add_timeline_step_number_column_in_timeline_action_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_action_comment', 'timeline_step_number', $this->integer()->notNull()->comment('Номер степа'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_075949_add_timeline_step_number_column_in_timeline_action_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_075949_add_timeline_step_number_column_in_timeline_action_comment cannot be reverted.\n";

        return false;
    }
    */
}
