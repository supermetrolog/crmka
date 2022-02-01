<?php

use yii\db\Migration;

/**
 * Class m220201_111540_add_competitor_company_id_column_and_drop_competitor_name_column_in_deal_table
 */
class m220201_111540_add_competitor_company_id_column_and_drop_competitor_name_column_in_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('deal', 'competitor_name');
        $this->addColumn('deal', 'competitor_company_id', $this->integer()->defaultValue(null)->comment('[СВЯЗЬ] с компанией (компания конкурент)'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220201_111540_add_competitor_company_id_column_and_drop_competitor_name_column_in_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_111540_add_competitor_company_id_column_and_drop_competitor_name_column_in_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
