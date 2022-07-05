<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%favorite_offer}}`.
 */
class m220705_092416_create_favorite_offer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%favorite_offer}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'complex_id' => $this->integer()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'original_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-favorite_offer-user_id',
            'favorite_offer',
            'user_id'
        );

        $this->addForeignKey(
            'fk-favorite_offer-user_id',
            'favorite_offer',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%favorite_offer}}');
    }
}
