<?php

use yii\db\Migration;

/**
 * Class m211129_142640_add_status_column_in_call_list_table
 */
class m211129_142640_add_status_column_in_call_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('call_list', 'status', $this->integer()->defaultValue(null)->comment('[флаг] событие после звонка (0 - отменил, 1 - сбросил, 2 - ответил, 3 - не ответил, 4 - другая причина'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211129_142640_add_status_column_in_call_list_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211129_142640_add_status_column_in_call_list_table cannot be reverted.\n";

        return false;
    }
    */
}
