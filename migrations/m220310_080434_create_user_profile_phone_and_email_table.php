<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile_phone_and_email}}`.
 */
class m220310_080434_create_user_profile_phone_and_email_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_profile_phone}}', [
            'id' => $this->primaryKey(),
            'user_profile_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с профилем юзера'),
            'phone' => $this->string()->notNull()->comment('номер телефона'),
        ]);
        $this->createTable('{{%user_profile_email}}', [
            'id' => $this->primaryKey(),
            'user_profile_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с профилем юзера'),
            'email' => $this->string()->notNull()->comment('email'),
        ]);

        $this->createIndex(
            'idx-user_profile_phone-user_profile_id',
            'user_profile_phone',
            'user_profile_id'
        );

        $this->createIndex(
            'idx-user_profile_email-user_profile_id',
            'user_profile_email',
            'user_profile_id'
        );

        $this->addForeignKey(
            'fk-user_profile_phone-user_profile_id',
            'user_profile_phone',
            'user_profile_id',
            'user_profile',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_profile_email-user_profile_id',
            'user_profile_email',
            'user_profile_id',
            'user_profile',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_profile_phone_and_email}}');
    }
}
