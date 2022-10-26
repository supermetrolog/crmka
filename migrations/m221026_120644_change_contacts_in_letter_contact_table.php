<?php

use yii\db\Migration;

/**
 * Class m221026_120644_change_contacts_in_letter_contact_table
 */
class m221026_120644_change_contacts_in_letter_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('letter_contact', 'email_or_phone_id');
        $this->addColumn('letter_contact', 'email_id', $this->integer()->comment("[СВЯЗЬ] с мейлами"));
        $this->addColumn('letter_contact', 'phone_id', $this->integer()->comment("[СВЯЗЬ] с телефонами"));
        $this->createIndex(
            'idx-letter_contact-email_id',
            'letter_contact',
            'email_id'
        );
        $this->createIndex(
            'idx-letter_contact-phone_id',
            'letter_contact',
            'phone_id'
        );

        $this->addForeignKey(
            'fk-letter_contact-email_id',
            'letter_contact',
            'email_id',
            'email',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-letter_contact-phone_id',
            'letter_contact',
            'phone_id',
            'phone',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221026_120644_change_contacts_in_letter_contact_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221026_120644_change_contacts_in_letter_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
