<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company_file}}`.
 */
class m211106_025946_create_company_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company_file}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'filename' => $this->string()->notNull(),
            'size' => $this->string()->notNull(),
            'type' => $this->string()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createIndex(
            'idx-company_file-company_id',
            'company_file',
            'company_id'
        );
        $this->addForeignKey(
            'fk-company_file-company_id',
            'company_file',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company_file}}');
    }
}
