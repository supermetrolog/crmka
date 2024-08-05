<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%chat_member_message_task}}`.
 */
class m240504_110934_drop_chat_member_message_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%chat_member_message_task}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('{{%chat_member_message_task}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
