<?php

use app\models\letter\Letter;
use yii\db\Migration;

/**
 * Class m230112_090609_add_contact_id_and_phone_and_email_columns_in_letter_contact_table
 */
class m230112_090609_add_contact_id_and_phone_and_email_columns_in_letter_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = "letter_contact";

        $this->addColumn($table, "contact_id", $this->integer()->notNull()->comment("[СВЯЗЬ] с таблицей контактов"));

        $this->createIndex(
            "idx-letter_contact-contact_id",
            $table,
            "contact_id",
        );

        $this->addForeignKey(
            "fk-letter_contact-contact_id",
            $table,
            "contact_id",
            "contact",
            "id",
            "CASCADE"
        );

        $this->dropForeignKey("fk-letter_contact-email_id", "letter_contact");
        $this->dropForeignKey("fk-letter_contact-phone_id", "letter_contact");
        $this->dropColumn($table, "email_id");
        $this->dropColumn($table, "phone_id");

        $this->addColumn($table, "phone", $this->string()->defaultValue(null)->comment("номер контакта"));
        $this->addColumn($table, "email", $this->string()->defaultValue(null)->comment("email контакта"));

        Letter::deleteAll();

        $this->addColumn("letter", "company_id", $this->integer()->notNull()->comment("[СВЯЗЬ] с таблицей компаний"));

        $this->createIndex(
            "idx-letter-company_id",
            "letter",
            "company_id",
        );

        $this->addForeignKey(
            "fk-letter-company_id",
            "letter",
            "company_id",
            "company",
            "id",
            "CASCADE"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk-letter_contact-contact_id", "letter_contact");

        $this->dropColumn("letter_contact", "contact_id");
        $this->dropColumn("letter_contact", "email");
        $this->dropColumn("letter_contact", "phone");

        $this->dropForeignKey("fk-letter-company_id", "letter");
        $this->dropColumn("letter", "company_id");

        $this->addColumn("letter_contact", "email_id", $this->integer());
        $this->addColumn("letter_contact", "phone_id", $this->integer());

        $this->addForeignKey(
            "fk-letter_contact-email_id",
            "letter_contact",
            "email_id",
            "email",
            "id",
            "CASCADE"
        );

        $this->addForeignKey(
            "fk-letter_contact-phone_id",
            "letter_contact",
            "phone_id",
            "phone",
            "id",
            "CASCADE"
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230112_090609_add_contact_id_and_phone_and_email_columns_in_letter_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
