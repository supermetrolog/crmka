<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_object}}`.
 */
class m210723_081759_create_timeline_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_object}}', [
            'id' => $this->primaryKey(),
            'timeline_action_id' => $this->integer()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'type' => $this->integer(2),
        ]);
        $this->createIndex(
            'idx-timeline_object-timeline_action',
            'timeline_object',
            'timeline_action_id'
        );

        $this->addForeignKey(
            'fk-timeline_object-timeline_action',
            'timeline_object',
            'timeline_action_id',
            'timeline_action',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timeline_object}}');
    }
}
