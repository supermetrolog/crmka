<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%timeline_step_object_comment}}`.
 */
class m220211_090735_drop_timeline_step_object_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%timeline_step_object_comment}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('{{%timeline_step_object_comment}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
