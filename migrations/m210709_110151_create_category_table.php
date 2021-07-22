<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m210709_110151_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer(11)->notNull(),
            'category' => $this->integer(11)->notNull(),
        ]);
        $this->createIndex(
            'idx-category-company_id',
            'category',
            'company_id'
        );

        $this->addForeignKey(
            'fk-category-company_id',
            'category',
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
        $this->dropTable('{{%category}}');
    }
}
