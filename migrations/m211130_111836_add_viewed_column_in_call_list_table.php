<?php

use yii\db\Migration;

/**
 * Class m211130_111836_add_viewed_column_in_call_list_table
 */
class m211130_111836_add_viewed_column_in_call_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('call_list', 'viewed', $this->integer()->defaultValue(0)->comment('[флаг] 0 - не запрошено, 1 - было запрошено, 2 - просмотренно в уведомлениях'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211130_111836_add_viewed_column_in_call_list_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211130_111836_add_viewed_column_in_call_list_table cannot be reverted.\n";

        return false;
    }
    */
}
