<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company_events_log}}`.
 */
class m230130_104447_create_company_events_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company_events_log}}', [
            'id' => $this->primaryKey(),
            'message' => $this->text()->defaultValue(null),
            'type' => $this->tinyInteger()->defaultValue(0)->comment("Тип события"),
            'company_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с компанией'),
            'user_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с пользователями'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex(
            'idx-company_events_log-company_id',
            'company_events_log',
            'company_id'
        );

        $this->createIndex(
            'idx-company_events_log-user_id',
            'company_events_log',
            'user_id'
        );

        $this->addForeignKey(
            'fk-company_events_log-company_id',
            'company_events_log',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-company_events_log-user_id',
            'company_events_log',
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
        $this->dropTable('{{%company_events_log}}');
    }
}
