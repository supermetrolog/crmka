<?php

use yii\db\Migration;

/**
 * Class m220124_101049_add_pasive_why_columns_in_company_table
 */
class m220124_101049_add_pasive_why_columns_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company', 'passive_why', $this->tinyInteger()->defaultValue(null)->comment('Причина пассива компании'));
        $this->addColumn('company', 'passive_why_comment', $this->string()->defaultValue(null)->comment('Комментарий почему компания пассив'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220124_101049_add_pasive_why_columns_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220124_101049_add_pasive_why_columns_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
