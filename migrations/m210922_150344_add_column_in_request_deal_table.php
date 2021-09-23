<?php

use yii\db\Migration;

/**
 * Class m210922_150344_add_column_in_request_deal_table
 */
class m210922_150344_add_column_in_request_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request_deal', 'object_id', $this->integer()->notNull());
        $this->addColumn('request_deal', 'complex_id', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210922_150344_add_column_in_request_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210922_150344_add_column_in_request_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
