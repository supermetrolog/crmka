<?php

use yii\db\Migration;

/**
 * Class m211221_090315_add_contacts_column_in_user_profile_table
 */
class m211221_090315_add_contacts_column_in_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_profile', 'contacts', $this->json()->defaultValue(null)->comment('JSON телефон и email'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211221_090315_add_contacts_column_in_user_profile_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_090315_add_contacts_column_in_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
