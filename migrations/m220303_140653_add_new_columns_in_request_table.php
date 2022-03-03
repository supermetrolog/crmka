<?php

use yii\db\Migration;

/**
 * Class m220303_140653_add_new_columns_in_request_table
 */
class m220303_140653_add_new_columns_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'water', $this->tinyInteger()->defaultValue(null)->comment('[ФЛАГ] наличие воды'));
        $this->addColumn('request', 'gaz', $this->tinyInteger()->defaultValue(null)->comment('[ФЛАГ] наличие газа'));
        $this->addColumn('request', 'steam', $this->tinyInteger()->defaultValue(null)->comment('[ФЛАГ] наличие пара'));
        $this->addColumn('request', 'shelving', $this->tinyInteger()->defaultValue(null)->comment('[ФЛАГ] наличие стелажей'));
        $this->addColumn('request', 'sewerage', $this->tinyInteger()->defaultValue(null)->comment('[ФЛАГ] наличие канализации'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220303_140653_add_new_columns_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_140653_add_new_columns_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
