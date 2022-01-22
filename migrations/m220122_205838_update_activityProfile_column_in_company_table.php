<?php

use yii\db\Migration;

/**
 * Class m220122_205838_update_activityProfile_column_in_company_table
 */
class m220122_205838_update_activityProfile_column_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('company', 'activityProfile', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220122_205838_update_activityProfile_column_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220122_205838_update_activityProfile_column_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
