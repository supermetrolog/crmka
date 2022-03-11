<?php

use yii\db\Migration;

/**
 * Class m220311_081911_alter_description_column_in_request_table
 */
class m220311_081911_alter_description_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('request', 'description', $this->text()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220311_081911_alter_description_column_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220311_081911_alter_description_column_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
