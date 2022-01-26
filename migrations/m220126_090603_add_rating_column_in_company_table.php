<?php

use yii\db\Migration;

/**
 * Class m220126_090603_add_rating_column_in_company_table
 */
class m220126_090603_add_rating_column_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $max_rating = 5;
        $this->addColumn('company', 'rating', $this->tinyInteger()->notNull()->defaultValue($max_rating)->comment('Важность компании (приоритет)'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220126_090603_add_rating_column_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220126_090603_add_rating_column_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
