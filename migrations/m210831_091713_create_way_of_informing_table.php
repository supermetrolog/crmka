<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%way_of_informing}}`.
 */
class m210831_091713_create_way_of_informing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%way_of_informing}}', [
            'id' => $this->primaryKey(),
            'contact_id' => $this->integer()->notNull(),
            'way' => $this->integer()->notNull()->defaultValue(0)
        ]);
        $this->createIndex(
            'idx-way_of_informing-contact_id',
            'way_of_informing',
            'contact_id'
        );

        $this->addForeignKey(
            'fk-way_of_informing-contact_id',
            'way_of_informing',
            'contact_id',
            'contact',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%way_of_informing}}');
    }
}
