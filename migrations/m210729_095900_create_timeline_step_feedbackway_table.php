<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_step_feedbackway}}`.
 */
class m210729_095900_create_timeline_step_feedbackway_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_step_feedbackway}}', [
            'id' => $this->primaryKey(),
            'timeline_step_id' => $this->integer()->notNull()->comment('[связь] с конкретным шагом таймлайна'),
            'way' => $this->integer()->comment('Способ получения обратной связи'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()
        ]);
        $this->createIndex(
            'idx-timeline_step_feedbackway-timeline_step_id',
            'timeline_step_feedbackway',
            'timeline_step_id'
        );

        $this->addForeignKey(
            'fk-timeline_step_feedbackway-timeline_step_id',
            'timeline_step_feedbackway',
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
        $this->dropTable('{{%timeline_step_feedbackway}}');
    }
}
