<?php

use yii\db\Migration;

/**
 * Class m210921_101216_add_name_column_in_request_deal_table
 */
class m210921_101216_add_name_column_in_request_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request_deal', 'name', $this->string()->comment('название сделки'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210921_101216_add_name_column_in_request_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210921_101216_add_name_column_in_request_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
