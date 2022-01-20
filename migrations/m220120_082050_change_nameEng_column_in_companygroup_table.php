<?php

use yii\db\Migration;

/**
 * Class m220120_082050_change_nameEng_column_in_companygroup_table
 */
class m220120_082050_change_nameEng_column_in_companygroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('companygroup', 'nameEng', $this->string()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220120_082050_change_nameEng_column_in_companygroup_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220120_082050_change_nameEng_column_in_companygroup_table cannot be reverted.\n";

        return false;
    }
    */
}
