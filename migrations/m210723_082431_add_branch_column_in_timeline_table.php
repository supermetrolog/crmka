<?php

use yii\db\Migration;

/**
 * Class m210723_082431_add_branch_column_in_timeline_table
 */
class m210723_082431_add_branch_column_in_timeline_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline', 'branch', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210723_082431_add_branch_column_in_timeline_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210723_082431_add_branch_column_in_timeline_table cannot be reverted.\n";

        return false;
    }
    */
}
