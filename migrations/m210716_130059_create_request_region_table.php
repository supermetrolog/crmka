<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_region}}`.
 */
class m210716_130059_create_request_region_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_region}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'region' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx-request_region-request_id',
            'request_region',
            'request_id'
        );

        $this->addForeignKey(
            'fk-request_region-request_id',
            'request_region',
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
        $this->dropTable('{{%request_region}}');
    }
}
