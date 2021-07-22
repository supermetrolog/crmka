<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_district}}`.
 */
class m210716_130233_create_request_district_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_district}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'district' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx-request_district-request_id',
            'request_district',
            'request_id'
        );

        $this->addForeignKey(
            'fk-request_district-request_id',
            'request_district',
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
        $this->dropTable('{{%request_district}}');
    }
}
