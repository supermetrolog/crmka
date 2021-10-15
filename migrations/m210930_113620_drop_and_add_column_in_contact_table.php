<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_add_column_in_contact}}`.
 */
class m210930_113620_drop_and_add_column_in_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('contact', 'first_name');
        $this->addColumn('contact', 'first_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "Poshel na hui";
        return false;
    }
}
