<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_gate_type}}`.
 */
class m210716_130139_create_request_gate_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_gate_type}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'gate_type' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx-request_gate_type-request_id',
            'request_gate_type',
            'request_id'
        );

        $this->addForeignKey(
            'fk-request_gate_type-request_id',
            'request_gate_type',
            'request_id',
            'request',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request_gate_type}}');
    }
}
