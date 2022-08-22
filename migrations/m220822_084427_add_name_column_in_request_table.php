<?php

use yii\db\Migration;

/**
 * Class m220822_084427_add_name_column_in_request_table
 */
class m220822_084427_add_name_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'name', $this->string()->comment("название"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220822_084427_add_name_column_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220822_084427_add_name_column_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
