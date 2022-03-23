<?php

use yii\db\Migration;

/**
 * Class m220323_134640_add_hangup_time_column_in_call_list_table
 */
class m220323_134640_add_hangup_time_column_in_call_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('call_list', 'hangup_timestamp', $this->timestamp()->defaultValue(null)->comment('Время окончания звонка'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220323_134640_add_hangup_time_column_in_call_list_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220323_134640_add_hangup_time_column_in_call_list_table cannot be reverted.\n";

        return false;
    }
    */
}
