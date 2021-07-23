<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%feedback_way}}`.
 */
class m210723_080918_create_feedback_way_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%feedback_way}}', [
            'id' => $this->primaryKey(),
            'timeline_action_id' => $this->integer()->notNull(),
            'way' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx-feedback_way-timeline_action',
            'feedback_way',
            'timeline_action_id'
        );

        $this->addForeignKey(
            'fk-feedback_way-timeline_action',
            'feedback_way',
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
        $this->dropTable('{{%feedback_way}}');
    }
}
