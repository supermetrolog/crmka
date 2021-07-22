<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contact}}`.
 */
class m210629_134443_create_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%contact}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'phone' => $this->string(255)->defaultValue(null),
            'email' => $this->string(255)->defaultValue(null),
            'first_name' => $this->string(255)->notNull(),
            'middle_name' => $this->string(255)->defaultValue(null),
            'last_name' => $this->string(255)->defaultValue(null),
            'comment' => $this->string(255)->defaultValue(null),
            'status' => $this->integer(11)->defaultValue(0),
            'type' => $this->integer(11)->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),

        ]);
        $this->createIndex(
            'idx-company-company_id',
            'contact',
            'company_id'
        );

        $this->addForeignKey(
            'fk-company-company_id',
            'contact',
            'company_id',
            'company',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%contact}}');
    }
}
