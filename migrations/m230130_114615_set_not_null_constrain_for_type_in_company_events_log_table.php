<?php

use yii\db\Migration;

/**
 * Class m230130_114615_set_not_null_constrain_for_type_in_company_events_log_table
 */
class m230130_114615_set_not_null_constrain_for_type_in_company_events_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("company_events_log", "type", $this->tinyInteger()->notNull()->comment("Тип события"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230130_114615_set_not_null_constrain_for_type_in_company_events_log_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230130_114615_set_not_null_constrain_for_type_in_company_events_log_table cannot be reverted.\n";

        return false;
    }
    */
}
