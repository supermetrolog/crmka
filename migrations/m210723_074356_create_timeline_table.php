<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline}}`.
 */
class m210723_074356_create_timeline_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'step' => $this->integer(2)->notNull(),
            'isBranch' => $this->integer(2)->notNull(),
            'datetime' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex(
            'idx-timeline-request_id',
            'timeline',
            'request_id'
        );

        $this->addForeignKey(
            'fk-timeline-request_id',
            'timeline',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timeline}}');
    }
}
