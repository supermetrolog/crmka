<?php

use yii\db\Migration;

/**
 * Class m220309_122546_add_isMain_column_in_contact_table
 */
class m220309_122546_add_isMain_column_in_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contact', 'isMain', $this->tinyInteger()->defaultValue(null)->comment('[ФЛАГ] основной контакт'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220309_122546_add_isMain_column_in_contact_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220309_122546_add_isMain_column_in_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
