<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%email}}`.
 */
class m210702_092927_create_email_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%email}}', [
            'id' => $this->primaryKey(),
            'contact_id' => $this->integer(11)->notNull(),
            'email' => $this->string(255)->notNull(),
        ]);
        $this->createIndex(
            'idx-contact-contact_id',
            'email',
            'contact_id'
        );

        $this->addForeignKey(
            'fk-contact-contact_id',
            'email',
            'contact_id',
            'contact',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email}}');
    }
}
