<?php

use yii\db\Migration;

/**
 * Class m230227_143651_add_columns_in_company_events_log_table
 */
class m230227_143651_add_columns_in_company_events_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company_events_log', 'question_id', $this->smallInteger()->defaultValue(null)->comment('номер вопроса, на который написан ответ'));
        $this->addColumn('company_events_log', 'question_parent', $this->smallInteger()->defaultValue(null)->comment('родитель вопроса, на который написан ответ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230227_143651_add_columns_in_company_events_log_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230227_143651_add_columns_in_company_events_log_table cannot be reverted.\n";

        return false;
    }
    */
}
