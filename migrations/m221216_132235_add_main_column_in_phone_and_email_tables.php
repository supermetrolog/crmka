<?php

use yii\db\Migration;

/**
 * Class m221216_132235_add_main_column_in_phone_and_email_tables
 */
class m221216_132235_add_main_column_in_phone_and_email_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('phone', 'isMain', $this->tinyInteger()->comment("Главный номер телефона контакта"));
        $this->addColumn('email', 'isMain', $this->tinyInteger()->comment("Главный email контакта"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221216_132235_add_main_column_in_phone_and_email_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221216_132235_add_main_column_in_phone_and_email_tables cannot be reverted.\n";

        return false;
    }
    */
}
