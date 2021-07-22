<?php

use yii\db\Migration;

/**
 * Class m210709_105833_add_active_column_in_company_table
 */
class m210709_105833_add_active_column_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company', 'active', $this->integer(11)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210709_105833_add_active_column_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210709_105833_add_active_column_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
