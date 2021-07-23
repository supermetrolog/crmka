<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_action}}`.
 */
class m210723_075335_create_timeline_action_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_action}}', [
            'id' => $this->primaryKey(),
            'timeline_id' => $this->integer()->notNull(),
            'done' => $this->integer(2),
            'negative' => $this->integer(2),
            'additional' => $this->integer(2),
            'date' => $this->timestamp(),
        ]);
        $this->createIndex(
            'idx-timeline_action-timeline_id',
            'timeline_action',
            'timeline_id'
        );

        $this->addForeignKey(
            'fk-timeline_action-timeline_id',
            'timeline_action',
            'timeline_id',
            'timeline',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timeline_action}}');
    }
}
