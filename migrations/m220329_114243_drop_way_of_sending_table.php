<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%way_of_sending}}`.
 */
class m220329_114243_drop_way_of_sending_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%way_of_sending}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('{{%way_of_sending}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
