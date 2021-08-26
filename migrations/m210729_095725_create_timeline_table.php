<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline}}`.
 */
class m210729_095725_create_timeline_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull()->comment('[связь] с запросами'),
            'consultant_id' => $this->integer()->notNull()->comment('[связь] с юзерами'),
            'status' => $this->integer(2)->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
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
        $this->createIndex(
            'idx-timeline-consultant_id',
            'timeline',
            'consultant_id'
        );

        $this->addForeignKey(
            'fk-timeline-consultant_id',
            'timeline',
            'consultant_id',
            'user',
            'id'
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
