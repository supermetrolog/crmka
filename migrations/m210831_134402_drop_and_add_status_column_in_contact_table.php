<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_add_status_column_in_contact}}`.
 */
class m210831_134402_drop_and_add_status_column_in_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('contact', 'status');
        $this->addColumn('contact', 'status', $this->integer(2)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "ANUS";
    }
}
