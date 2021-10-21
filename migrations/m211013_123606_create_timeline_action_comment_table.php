<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_action_comment}}`.
 */
class m211013_123606_create_timeline_action_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_action_comment}}', [
            'id' => $this->primaryKey(),
            'timeline_step_id' => $this->integer()->notNull()->comment('[связь]'),
            'action' => $this->integer()->comment('тип действия'),
            'comment' => $this->string()->notNull()->comment('комментарий к действию'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex(
            'idx-timeline_action_comment-timeline_step_id',
            'timeline_action_comment',
            'timeline_step_id'
        );
        $this->addForeignKey(
            'fk-timeline_action_comment-timeline_step_id',
            'timeline_action_comment',
            'timeline_step_id',
            'timeline_step',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timeline_action_comment}}');
    }
}
