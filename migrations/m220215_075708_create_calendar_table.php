<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%calendar}}`.
 */
class m220215_075708_create_calendar_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%calendar}}', [
            'id' => $this->primaryKey(),
            'consultant_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с юзером'),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->defaultValue(null),
            'startDate' => $this->timestamp()->notNull()->comment('Дата начала события'),
            'endDate' => $this->timestamp()->comment('Дата конца события'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('[ФЛАГ] статус'),
            'period_notify' => $this->integer()->defaultValue(null)->comment('Частота уведомлений (раз в час, раз в день и т.д)'),
            'lastNotifyDate' => $this->timestamp()->defaultValue(null)->comment('Дата последнего уведомления'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-calendar-consultant_id',
            'calendar',
            'consultant_id'
        );
        $this->addForeignKey(
            'fk-calendar-consultant_id',
            'calendar',
            'consultant_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%calendar}}');
    }
}
