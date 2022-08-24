<?php

use yii\db\Migration;

/**
 * Class m220824_085552_add_region_neardy_column_in_request_table
 */
class m220824_085552_add_region_neardy_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("request", "region_neardy", $this->tinyInteger()->comment("[флаг] регионы рядом"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220824_085552_add_region_neardy_column_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220824_085552_add_region_neardy_column_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
