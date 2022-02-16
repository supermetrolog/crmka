<?php

use yii\db\Migration;

/**
 * Class m220216_105747_add_postion_unknown_column_in_contact_table
 */
class m220216_105747_add_postion_unknown_column_in_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contact', 'position_unknown', $this->tinyInteger()->notNull()->defaultValue(0)->comment('[ФЛАГ] если должность неизвестна'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220216_105747_add_postion_unknown_column_in_contact_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220216_105747_add_postion_unknown_column_in_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
