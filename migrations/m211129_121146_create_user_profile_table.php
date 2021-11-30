<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile}}`.
 */
class m211129_121146_create_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('[связь] с юзером'),
            'first_name' => $this->string()->defaultValue('DEFAULT_FIRST_NAME'),
            'middle_name' => $this->string()->defaultValue('DEFAULT_MIDDLE_NAME'),
            'last_name' => $this->string()->defaultValue('DEFAULT_LAST_NAME'),
            'caller_id' => $this->string()->defaultValue(null)->unique()->comment('Номер в системе Asterisk'),
        ]);
        $this->createIndex('idx-user_profile-user', 'user_profile', 'user_id');
        $this->addForeignKey('fk-user_profile-user_id', 'user_profile', 'user_id', 'user', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_profile}}');
    }
}
