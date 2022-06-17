<?php

use yii\db\Migration;

/**
 * Class m220617_133816_add_nameBrand_column_in_company_table
 */
class m220617_133816_add_nameBrand_column_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company', 'nameBrand', $this->string()->defaultValue(null)->comment("название бренда"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220617_133816_add_nameBrand_column_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220617_133816_add_nameBrand_column_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
