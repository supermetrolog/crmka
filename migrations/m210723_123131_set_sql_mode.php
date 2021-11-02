<?php

use yii\db\Migration;

/**
 * Class m210723_123131_set_sql_mode
 */
class m210723_123131_set_sql_mode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $sql = "set global sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
        // $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210723_123131_set_sql_mode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210723_123131_set_sql_mode cannot be reverted.\n";

        return false;
    }
    */
}
