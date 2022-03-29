<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%way_of_sending}}`.
 */
class m220329_112922_create_way_of_sending_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%way_of_sending}}', [
            'id' => $this->primaryKey(),
            'user_sended_data_id' => $this->integer()->notNull(),
            'way' => $this->tinyInteger()->notNull()->comment('[ФЛАГ] способ отправки сообщения клиенту')
        ]);

        $this->createIndex('idx=way_of_sending-user_sended_data_id', 'way_of_sending', 'user_sended_data_id');
        $this->addForeignKey('fk-way_of_sending-user_sended_data_id', 'way_of_sending', 'user_sended_data_id', 'user_sended_data', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%way_of_sending}}');
    }
}
