<?php

use yii\db\Migration;

/**
 * Class m211208_110514_add_avatar_column_in_user_profile_table
 */
class m211208_110514_add_avatar_column_in_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_profile', 'avatar', $this->string()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211208_110514_add_avatar_column_in_user_profile_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211208_110514_add_avatar_column_in_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
