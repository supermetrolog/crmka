<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline_step}}`.
 */
class m210729_095736_create_timeline_step_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_step}}', [
            'id' => $this->primaryKey(),
            'timeline_id' => $this->integer()->notNull()->comment('[связь] с таймлайном'),
            'number' => $this->integer()->notNull()->comment('номер шага'),
            'comment' => $this->string()->comment('общий комментарий к шагу'),
            'done' => $this->integer()->comment('[флаг] ГОТОВО - используется для любого шага'),
            'negative' => $this->integer()->comment('[флаг] ОТРИЦАНИЕ - используется для любого шага'),
            'additional' => $this->integer()->comment('[флаг] ДОПОЛНИТЕЛЬНЫЙ ФЛАГ - используется для любого шага'),
            'date' => $this->timestamp()->comment('ДАТА используется для любого шага'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
        ]);
        $this->createIndex(
            'idx-timeline_step-timeline_id',
            'timeline_step',
            'timeline_id'
        );

        $this->addForeignKey(
            'fk-timeline_step-timeline_id',
            'timeline_step',
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
        $this->dropTable('{{%timeline_step}}');
    }
}
