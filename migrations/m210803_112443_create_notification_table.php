<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification}}`.
 */
class m210803_112443_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'consultant_id' => $this->integer()->notNull()->comment('[связь] с юзером'),
            'title' => $this->string()->comment('заголовок оповещения'),
            'body' => $this->string()->notNull()->comment('[html]текс оповещения'),
            'type' => $this->integer(2)->notNull()->comment('тип оповещения'),
            'status' => $this->integer(2)->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex(
            'idx-notification-consultant_id',
            'notification',
            'consultant_id'
        );

        $this->addForeignKey(
            'fk-notification-consultant_id',
            'notification',
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
        $this->dropTable('{{%notification}}');
    }
}
