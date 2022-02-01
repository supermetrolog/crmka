<?php

use yii\db\Migration;

/**
 * Class m220201_095032_add_competitor_name_column_in_deal_table
 */
class m220201_095032_add_competitor_name_column_in_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('deal', 'competitor_name', $this->string()->defaultValue(null)->comment('Название компани  конкурента'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220201_095032_add_competitor_name_column_in_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_095032_add_competitor_name_column_in_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
