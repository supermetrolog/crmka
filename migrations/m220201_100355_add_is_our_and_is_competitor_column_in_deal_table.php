<?php

use yii\db\Migration;

/**
 * Class m220201_100355_add_is_our_and_is_competitor_column_in_deal_table
 */
class m220201_100355_add_is_our_and_is_competitor_column_in_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('deal', 'is_our', $this->tinyInteger()->defaultValue(1)->comment('[ФЛАГ] принадлежит ли сделка нашей компании'));
        $this->addColumn('deal', 'is_competitor', $this->tinyInteger()->defaultValue(0)->comment('[ФЛАГ] принадлежит ли сделка конкурентам'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('deal', 'is_our');
        $this->dropColumn('deal', 'is_competitor');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_100355_add_is_our_and_is_competitor_column_in_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
