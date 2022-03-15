<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_object_comment}}`.
 */
class m220211_081203_create_timeline_object_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_step_object_comment}}', [
            'id' => $this->primaryKey(),
            'timeline_step_object_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с объектами этапов таймлайна'),
            'comment' => $this->string()->notNull()->comment('Комментарий к объекту'),
        ]);

        $this->createIndex(
            'idx-timeline_step_object_comment-timeline_step_object_id',
            'timeline_step_object_comment',
            'timeline_step_object_id'
        );
        $this->addForeignKey(
            'fk-timeline_step_object_comment-timeline_step_object_id',
            'timeline_step_object_comment',
            'timeline_step_object_id',
            'timeline_step_object',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timeline_step_object_comment}}');
    }
}
