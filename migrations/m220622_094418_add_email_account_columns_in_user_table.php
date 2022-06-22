<?php

use yii\db\Migration;

/**
 * Class m220622_094418_add_email_account_columns_in_user_table
 */
class m220622_094418_add_email_account_columns_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user", "email_username", $this->string()->comment("email username for smtp service"));
        $this->addColumn("user", "email_password", $this->string()->comment("email password for smtp service"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220622_094418_add_email_account_columns_in_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_094418_add_email_account_columns_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
