<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%request_deal}}`.
 */
class m220201_095203_drop_request_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%request_deal}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('{{%request_deal}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
