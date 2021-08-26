<?php

use yii\db\Migration;

/**
 * Class m210824_111446_remove_adtiDustOnly_column_and_add_antiDustOnly_in_request_table
 */
class m210824_111446_remove_adtiDustOnly_column_and_add_antiDustOnly_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('request', 'antiDustOnly');
        $this->addColumn('request', 'antiDustOnly', $this->boolean()->defaultValue(0)->comment('[флаг] Только антипыль'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210824_111446_remove_adtiDustOnly_column_and_add_antiDustOnly_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210824_111446_remove_adtiDustOnly_column_and_add_antiDustOnly_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
