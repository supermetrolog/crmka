<?php

use yii\db\Migration;

/**
 * Class m220520_132913_add_status_column_for_deal_table
 */
class m220520_132913_add_status_column_for_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('deal', 'status', $this->tinyInteger(2)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220520_132913_add_status_column_for_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220520_132913_add_status_column_for_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
