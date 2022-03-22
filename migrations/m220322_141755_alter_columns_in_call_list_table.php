<?php

use yii\db\Migration;

/**
 * Class m220322_141755_alter_columns_in_call_list_table
 */
class m220322_141755_alter_columns_in_call_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('call_list', 'status');
        $this->addColumn('call_list', 'status', $this->tinyInteger()->notNull()->defaultValue(0));

        $this->dropColumn('call_list', 'viewed');
        $this->addColumn('call_list', 'call_ended_status', $this->string()->defaultValue(null));

        $this->addColumn('call_list', 'updated_at', $this->timestamp()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220322_141755_alter_columns_in_call_list_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220322_141755_alter_columns_in_call_list_table cannot be reverted.\n";

        return false;
    }
    */
}
