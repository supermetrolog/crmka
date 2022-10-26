<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%letter}}`.
 */
class m221025_112859_create_letter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%letter}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment("[СВЯЗЬ] с таблицей юзеров"),
            'subject' => $this->string()->comment("Тема письма"),
            'body' => $this->text()->comment("Текст письма"),
            'created_at' => $this->timestamp()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment("1 - отправлено, 0 - ошибка"),
        ]);

        $this->createIndex(
            'idx-letter-user_id',
            'letter',
            'user_id',
        );
        $this->addForeignKey(
            'fk-letter-user_id',
            'letter',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createTable("letter_contact", [
            'id' => $this->primaryKey(),
            'letter_id' => $this->integer()->notNull()->comment("[СВЯЗЬ] с отправленными письмами"),
            'email_or_phone_id' => $this->integer()->notNull()->comment("[СВЯЗЬ] с таблицей мейлов или номеров телефонов"),
        ]);

        $this->createIndex(
            'idx-letter_contact-letter_id',
            'letter_contact',
            'letter_id'
        );

        $this->addForeignKey(
            'fk-letter_contact-letter_id',
            'letter_contact',
            'letter_id',
            'letter',
            'id'
        );

        $this->createTable("letter_offer", [
            'id' => $this->primaryKey(),
            'letter_id' => $this->integer()->notNull()->comment("[СВЯЗЬ] с отправленными письмами"),
            'original_id' => $this->integer()->notNull()->comment("[СВЯЗЬ] с предложениями"),
            'object_id' => $this->integer()->notNull()->comment("[СВЯЗЬ] с объектами"),
            'type_id' => $this->tinyInteger()->notNull()->comment("Тип предложения (1,2,3)")
        ]);

        $this->createIndex(
            'idx-letter_offer-letter_id',
            'letter_offer',
            'letter_id'
        );

        $this->addForeignKey(
            'fk-letter_offer-letter_id',
            'letter_offer',
            'letter_id',
            'letter',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%letter}}');
    }
}
