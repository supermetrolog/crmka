<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_step_object}}`.
 */
class m210729_101029_create_timeline_step_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_step_object}}', [
            'id' => $this->primaryKey(),
            'timeline_step_id' => $this->integer()->notNull()->comment('[связь] с конкретным шагом таймлайна'),
            'object_id' => $this->integer()->notNull()->comment('ID объекта'),
            'status' => $this->integer(),
            'option' => $this->integer()->comment('Дополнительные флаги для объекта'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()
        ]);
        $this->createIndex(
            'idx-timeline_step_object-timeline_step_id',
            'timeline_step_object',
            'timeline_step_id'
        );

        $this->addForeignKey(
            'fk-timeline_step_object-timeline_step_id',
            'timeline_step_object',
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
        $this->dropTable('{{%timeline_step_object}}');
    }
}
