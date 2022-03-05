<?php

use yii\db\Migration;

/**
 * Class m220305_144635_add_longitude_and_latitude_column_in_company_table
 */
class m220305_144635_add_longitude_and_latitude_column_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company', 'latitude', $this->string()->defaultValue(null)->comment('широта'));
        $this->addColumn('company', 'longitude', $this->string()->defaultValue(null)->comment('долгота'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220305_144635_add_longitude_and_latitude_column_in_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220305_144635_add_longitude_and_latitude_column_in_company_table cannot be reverted.\n";

        return false;
    }
    */
}
