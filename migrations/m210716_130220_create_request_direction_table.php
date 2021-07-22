<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_direction}}`.
 */
class m210716_130220_create_request_direction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_direction}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'direction' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx-request_direction-request_id',
            'request_direction',
            'request_id'
        );

        $this->addForeignKey(
            'fk-request_direction-request_id',
            'request_direction',
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
        $this->dropTable('{{%request_direction}}');
    }
}
