<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%call_list}}`.
 */
class m211129_122126_create_call_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%call_list}}', [
            'id' => $this->primaryKey(),
            'caller_id' => $this->string()->notNull()->comment('[связь] с user_profile (номер в системе Asterisk)'),
            'from' => $this->string()->notNull()->comment('кто звонит'),
            'to' => $this->string()->notNull()->comment('кому звонят'),
            'type' => $this->smallInteger()->notNull()->comment('[флаг] тип звонка (0 - исходящий / 1 - входящий'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
        $this->createIndex('idx-call_list-user_profile', 'call_list', 'caller_id');
        $this->addForeignKey('fk-call_list-caller_id', 'call_list', 'caller_id', 'user_profile', 'caller_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call_list}}');
    }
}
