<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_object_type}}`.
 */
class m210716_130158_create_request_object_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_object_type}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'object_type' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx-request_object_type-request_id',
            'request_object_type',
            'request_id'
        );

        $this->addForeignKey(
            'fk-request_object_type-request_id',
            'request_object_type',
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
        $this->dropTable('{{%request_object_type}}');
    }
}
