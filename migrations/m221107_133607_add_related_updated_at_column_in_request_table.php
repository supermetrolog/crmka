<?php

use yii\db\Migration;

/**
 * Class m221107_133607_add_related_updated_at_column_in_request_table
 */
class m221107_133607_add_related_updated_at_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'related_updated_at', $this->timestamp()->defaultValue(null)->comment("дата последнего обновления связанных с запросом сущностей"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221107_133607_add_related_updated_at_column_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221107_133607_add_related_updated_at_column_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
