<?php

use yii\db\Migration;

/**
 * Class m220823_120647_add_outside_mkad_column_in_request_table
 */
class m220823_120647_add_outside_mkad_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'outside_mkad', $this->tinyInteger()->comment("Вне мкад (если выбран регоин МОСКВА)"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220823_120647_add_outside_mkad_column_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220823_120647_add_outside_mkad_column_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
