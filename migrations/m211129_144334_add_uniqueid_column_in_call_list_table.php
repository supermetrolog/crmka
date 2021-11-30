<?php

use yii\db\Migration;

/**
 * Class m211129_144334_add_uniqueid_column_in_call_list_table
 */
class m211129_144334_add_uniqueid_column_in_call_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('call_list', 'status');
        $this->addColumn('call_list', 'status', $this->string()->defaultValue(null)->comment('чем закончился звонок'));
        $this->addColumn('call_list', 'uniqueid', $this->string()->unique()->comment('realtime call unique ID'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211129_144334_add_uniqueid_column_in_call_list_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211129_144334_add_uniqueid_column_in_call_list_table cannot be reverted.\n";

        return false;
    }
    */
}
