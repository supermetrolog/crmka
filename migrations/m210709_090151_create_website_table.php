<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%website}}`.
 */
class m210709_090151_create_website_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%website}}', [
            'id' => $this->primaryKey(),
            'contact_id' => $this->integer(11)->notNull(),
            'website' => $this->string(255)->notNull(),
        ]);
        $this->createIndex(
            'idx-website-contact_id',
            'website',
            'contact_id'
        );

        $this->addForeignKey(
            'fk-website-contact_id',
            'website',
            'contact_id',
            'contact',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%website}}');
    }
}
