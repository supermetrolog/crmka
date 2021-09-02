<?php

use yii\db\Migration;

/**
 * Class m210831_083000_add_type_id_column_in_timelineStepObjects_table
 */
class m210831_083000_add_type_id_column_in_timelineStepObjects_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_step_object', 'type_id', $this->integer()->defaultValue(1)->comment("Херня для API"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210831_083000_add_type_id_column_in_timelineStepObjects_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210831_083000_add_type_id_column_in_timelineStepObjects_table cannot be reverted.\n";

        return false;
    }
    */
}
