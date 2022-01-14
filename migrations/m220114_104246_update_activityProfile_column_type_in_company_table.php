<?php

use yii\db\Migration;

/**
 * Class m220114_104246_update_activityProfile_column_type_in_company_table
 */
class m220114_104246_update_activityProfile_column_type_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('company', 'activityProfile', $this->string()->notNull()->comment('Профиль деятельности'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220114_104246_update_activityProfile_column_type_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220114_104246_update_activityProfile_column_type_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
