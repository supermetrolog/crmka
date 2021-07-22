<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%companyGroup}}`.
 */
class m210629_090406_create_companyGroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%companyGroup}}', [
            'id' => $this->primaryKey(),
            'nameEng' => $this->string(255)->notNull(),
            'nameRu' => $this->string(255)->notNull(),
            'description' => $this->text()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%companyGroup}}');
    }
}
