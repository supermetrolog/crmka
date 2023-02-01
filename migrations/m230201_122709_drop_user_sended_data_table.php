<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%user_sended_data}}`.
 */
class m230201_122709_drop_user_sended_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%user_sended_data}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('{{%user_sended_data}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
