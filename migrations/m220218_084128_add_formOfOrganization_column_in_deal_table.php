<?php

use yii\db\Migration;

/**
 * Class m220218_084128_add_formOfOrganization_column_in_deal_table
 */
class m220218_084128_add_formOfOrganization_column_in_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('deal', 'formOfOrganization', $this->tinyInteger()->defaultValue(null)->comment('форма организации'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220218_084128_add_formOfOrganization_column_in_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220218_084128_add_formOfOrganization_column_in_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
