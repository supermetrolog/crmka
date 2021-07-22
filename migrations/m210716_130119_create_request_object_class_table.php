<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_object_class}}`.
 */
class m210716_130119_create_request_object_class_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_object_class}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'object_class' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx-request_object_class-request_id',
            'request_object_class',
            'request_id'
        );

        $this->addForeignKey(
            'fk-request_object_class-request_id',
            'request_object_class',
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
        $this->dropTable('{{%request_object_class}}');
    }
}
