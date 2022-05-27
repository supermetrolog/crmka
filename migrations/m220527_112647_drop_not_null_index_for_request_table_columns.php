<?php

use yii\db\Migration;

/**
 * Class m220527_112647_drop_not_null_index_for_request_table_columns
 */
class m220527_112647_drop_not_null_index_for_request_table_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('request', 'distanceFromMKADnotApplicable', $this->tinyInteger(1)->comment('[ФЛАГ] Удаленность от МКАД неприменима'));
        $this->alterColumn('request', 'expressRequest', $this->tinyInteger(1)->comment('[ФЛАГ] Срочный запрос'));
        $this->alterColumn('request', 'status', $this->tinyInteger(2)->comment('[ФЛАГ] Статус'));
        $this->alterColumn('request', 'haveCranes', $this->tinyInteger(1)->comment('[ФЛАГ] Наличие кранов'));
        $this->alterColumn('request', 'trainLine', $this->tinyInteger(1)->comment('[ФЛАГ] Наличие Ж/Д ветки'));
        $this->alterColumn('request', 'water', $this->tinyInteger(1)->comment('[ФЛАГ] Наличие воды'));
        $this->alterColumn('request', 'gaz', $this->tinyInteger(1)->comment('[ФЛАГ] Наличие газа'));
        $this->alterColumn('request', 'steam', $this->tinyInteger(1)->comment('[ФЛАГ] Наличиние пара'));
        $this->alterColumn('request', 'shelving', $this->tinyInteger(1)->comment('[ФЛАГ] Наличие стеллажей'));
        $this->alterColumn('request', 'sewerage', $this->tinyInteger(1)->comment('[ФЛАГ] Наличие канализации'));
        $this->alterColumn('request', 'firstFloorOnly', $this->tinyInteger(1)->comment('[ФЛАГ] Только 1-й этаж'));
        $this->alterColumn('request', 'antiDustOnly', $this->tinyInteger(1)->comment('[ФЛАГ] Только антипыль'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220527_112647_drop_not_null_index_for_request_table_columns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220527_112647_drop_not_null_index_for_request_table_columns cannot be reverted.\n";

        return false;
    }
    */
}
