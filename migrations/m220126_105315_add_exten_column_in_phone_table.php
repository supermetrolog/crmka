<?php

use yii\db\Migration;

/**
 * Class m220126_105315_add_exten_column_in_phone_table
 */
class m220126_105315_add_exten_column_in_phone_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('phone', 'exten', $this->string()->defaultValue(null)->comment('Добавочный номер'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220126_105315_add_exten_column_in_phone_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220126_105315_add_exten_column_in_phone_table cannot be reverted.\n";

        return false;
    }
    */
}
