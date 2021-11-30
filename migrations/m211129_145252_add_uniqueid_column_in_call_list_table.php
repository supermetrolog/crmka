<?php

use yii\db\Migration;

/**
 * Class m211129_145252_add_uniqueid_column_in_call_list_table
 */
class m211129_145252_add_uniqueid_column_in_call_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('call_list', 'uniqueid');

        $this->addColumn('call_list', 'uniqueid', $this->string()->comment('realtime call unique ID'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211129_145252_add_uniqueid_column_in_call_list_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211129_145252_add_uniqueid_column_in_call_list_table cannot be reverted.\n";

        return false;
    }
    */
}
