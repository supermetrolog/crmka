<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%productRange}}`.
 */
class m210709_112237_create_productRange_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%productRange}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer(11)->notNull(),
            'product' => $this->string(255)->notNull(),
        ]);
        $this->createIndex(
            'idx-productRange-company_id',
            'productRange',
            'company_id'
        );

        $this->addForeignKey(
            'fk-productRange-company_id',
            'productRange',
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
        $this->dropTable('{{%productRange}}');
    }
}
