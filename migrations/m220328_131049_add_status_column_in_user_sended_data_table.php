<?php

use yii\db\Migration;

/**
 * Class m220328_131049_add_status_column_in_user_sended_data_table
 */
class m220328_131049_add_status_column_in_user_sended_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_sended_data', 'status', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220328_131049_add_status_column_in_user_sended_data_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220328_131049_add_status_column_in_user_sended_data_table cannot be reverted.\n";

        return false;
    }
    */
}
