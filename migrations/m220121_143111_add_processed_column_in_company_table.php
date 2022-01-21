<?php

use yii\db\Migration;

/**
 * Class m220121_143111_add_processed_column_in_company_table
 */
class m220121_143111_add_processed_column_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company', 'processed', $this->tinyInteger()->comment('[ФЛАГ] Обработано'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220121_143111_add_processed_column_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220121_143111_add_processed_column_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
