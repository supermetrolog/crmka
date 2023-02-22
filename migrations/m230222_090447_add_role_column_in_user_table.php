<?php

use yii\db\Migration;

/**
 * Class m230222_090447_add_role_column_in_user_table
 */
class m230222_090447_add_role_column_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'role', $this->smallInteger()->defaultValue(1)->notNull()->comment('роль пользователя'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230222_090447_add_role_column_in_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230222_090447_add_role_column_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
