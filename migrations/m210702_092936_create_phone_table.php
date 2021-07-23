<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%phone}}`.
 */
class m210702_092936_create_phone_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%phone}}', [
            'id' => $this->primaryKey(),
            'contact_id' => $this->integer(11)->notNull(),
            'phone' => $this->string(255)->notNull(),
        ]);
        $this->createIndex(
            'idx-phone-contact_id',
            'phone',
            'contact_id'
        );

        $this->addForeignKey(
            'fk-phone-contact_id',
            'phone',
            'contact_id',
            'contact',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%phone}}');
    }
}
