<?php

use yii\db\Migration;

/**
 * Class m220218_083156_add_created_at_and_updated_at_column_in_deal_table
 */
class m220218_083156_add_created_at_and_updated_at_column_in_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('deal', 'created_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('deal', 'updated_at', $this->timestamp()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220218_083156_add_created_at_and_updated_at_column_in_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220218_083156_add_created_at_and_updated_at_column_in_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
