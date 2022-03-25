<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_sended_data}}`.
 */
class m220325_120130_create_user_sended_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_sended_data}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'contact' => $this->string()->notNull(),
            'contact_type' => $this->tinyInteger()->notNull(),
            'type' => $this->tinyInteger()->notNull(),
            'description' => $this->text()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex('idx-user_sended_data-user_id', 'user_sended_data', 'user_id');
        $this->addForeignKey('fk-user_sended_data-user_id', 'user_sended_data', 'user_id', 'user', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_sended_data}}');
    }
}
