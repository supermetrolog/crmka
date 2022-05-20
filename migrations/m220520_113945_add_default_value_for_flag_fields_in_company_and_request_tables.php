<?php

use yii\db\Migration;

/**
 * Class m220520_113945_add_default_value_for_flag_fields_in_company_and_request_tables
 */
class m220520_113945_add_default_value_for_flag_fields_in_company_and_request_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('company', 'processed', $this->tinyInteger(2)->notNull()->defaultValue(0)->comment('[ФЛАГ] Обработано'));
        $this->alterColumn('company', 'status', $this->tinyInteger(2)->notNull()->defaultValue(0)->comment('[ФЛАГ] Статус'));
        $this->alterColumn('request', 'distanceFromMKADnotApplicable', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Удаленность от МКАД неприменима'));
        $this->alterColumn('request', 'expressRequest', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Срочный запрос'));
        $this->alterColumn('request', 'status', $this->tinyInteger(2)->notNull()->defaultValue(0)->comment('[ФЛАГ] Статус'));
        $this->alterColumn('request', 'haveCranes', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Наличие кранов'));
        $this->alterColumn('request', 'trainLine', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Наличие Ж/Д ветки'));
        $this->alterColumn('request', 'water', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Наличие воды'));
        $this->alterColumn('request', 'gaz', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Наличие газа'));
        $this->alterColumn('request', 'steam', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Наличиние пара'));
        $this->alterColumn('request', 'shelving', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Наличие стеллажей'));
        $this->alterColumn('request', 'sewerage', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Наличие канализации'));
        $this->alterColumn('request', 'firstFloorOnly', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Только 1-й этаж'));
        $this->alterColumn('request', 'antiDustOnly', $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('[ФЛАГ] Только антипыль'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220520_113945_add_default_value_for_flag_fields_in_company_and_request_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220520_113945_add_default_value_for_flag_fields_in_company_and_request_tables cannot be reverted.\n";

        return false;
    }
    */
}
