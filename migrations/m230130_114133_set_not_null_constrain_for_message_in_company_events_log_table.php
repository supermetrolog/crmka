<?php

use yii\db\Migration;

/**
 * Class m230130_114133_set_not_null_constrain_for_message_in_company_events_log_table
 */
class m230130_114133_set_not_null_constrain_for_message_in_company_events_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('company_events_log', 'message', $this->text()->notNull()->comment("Текст события"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230130_114133_set_not_null_constrain_for_message_in_company_events_log_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230130_114133_set_not_null_constrain_for_message_in_company_events_log_table cannot be reverted.\n";

        return false;
    }
    */
}
