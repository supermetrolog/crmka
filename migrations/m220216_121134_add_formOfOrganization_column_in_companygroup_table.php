<?php

use yii\db\Migration;

/**
 * Class m220216_121134_add_formOfOrganization_column_in_companygroup_table
 */
class m220216_121134_add_formOfOrganization_column_in_companygroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('companygroup', 'formOfOrganization', $this->tinyInteger()->defaultValue(null)->comment('[ФЛАГ] форма организации'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220216_121134_add_formOfOrganization_column_in_companygroup_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220216_121134_add_formOfOrganization_column_in_companygroup_table cannot be reverted.\n";

        return false;
    }
    */
}
