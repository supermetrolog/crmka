<?php

use yii\db\Migration;

/**
 * Class m230118_113146_change_contact_id_column_in_letter_contact_table
 */
class m230118_113146_change_contact_id_column_in_letter_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("letter_contact", "contact_id", $this->integer()->defaultValue(null)->comment("[СВЯЗЬ] с контактами"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230118_113146_change_contact_id_column_in_letter_contact_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230118_113146_change_contact_id_column_in_letter_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
