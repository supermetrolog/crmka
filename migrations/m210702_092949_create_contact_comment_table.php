<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m210702_092949_create_contact_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%contact_comment}}', [
            'id' => $this->primaryKey(),
            'contact_id' => $this->integer(),
            'author_id' => $this->integer()->notNull(),
            'comment' => $this->string()->notNull(),
        ]);

        $this->createIndex(
            'idx-comment-contact_id',
            'contact_comment',
            'contact_id'
        );

        $this->addForeignKey(
            'fk-comment-contact_id',
            'contact_comment',
            'contact_id',
            'contact',
            'id',
        );
        $this->createIndex(
            'idx-author-contact_id',
            'contact_comment',
            'author_id'
        );

        $this->addForeignKey(
            'fk-author-author_id',
            'contact_comment',
            'author_id',
            'user',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%contact_comment}}');
    }
}
