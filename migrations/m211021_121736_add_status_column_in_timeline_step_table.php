<?php

use yii\db\Migration;

/**
 * Class m211021_121736_add_status_column_in_timeline_step_table
 */
class m211021_121736_add_status_column_in_timeline_step_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_step', 'status', $this->integer(2)->defaultValue(0)->comment('[ФЛАГ]'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211021_121736_add_status_column_in_timeline_step_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211021_121736_add_status_column_in_timeline_step_table cannot be reverted.\n";

        return false;
    }
    */
}
