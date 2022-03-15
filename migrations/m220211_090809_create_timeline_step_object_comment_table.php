<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_step_object_comment}}`.
 */
class m220211_090809_create_timeline_step_object_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_step_object_comment}}', [
            'id' => $this->primaryKey(),
            'timeline_step_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] часть составного внешнего ключа'),
            'object_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] часть составного внешнего ключа'),
            'comment' => $this->string()->notNull()->comment('комментарий к отправленному или добавленному объекту'),
        ]);

        $this->createIndex(
            'idx-timeline_step_object_comment-timeline_step_id',
            'timeline_step_object_comment',
            'timeline_step_id'
        );
        $this->addForeignKey(
            'fk-timeline_step_object_comment-timeline_step_id',
            'timeline_step_object_comment',
            'timeline_step_id',
            'timeline_step_object',
            'timeline_step_id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-timeline_step_object_comment-object_id',
            'timeline_step_object_comment',
            'object_id'
        );
        $this->createIndex(
            'idx-timeline_step_object-object_id',
            'timeline_step_object',
            'object_id'
        );
        $this->addForeignKey(
            'fk-timeline_step_object_comment-object_id',
            'timeline_step_object_comment',
            'object_id',
            'timeline_step_object',
            'object_id',
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
