<?php

use yii\db\Migration;

/**
 * Class m221017_153845_add_contact_id_column_in_request_table
 */
class m221017_153845_add_contact_id_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("request", "contact_id", $this->integer()->comment("[СВЯЗЬ] с контактом"));

        $this->createIndex("idx-request-contact_id", "request", "contact_id");
        $this->addForeignKey("fk-request-contact_id", "request", "contact_id", "contact", "id", "CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221017_153845_add_contact_id_column_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221017_153845_add_contact_id_column_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
