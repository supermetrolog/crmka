<?php

use yii\db\Migration;

/**
 * Class m220901_122938_add_columns_in_timeline_step_object_table
 */
class m220901_122938_add_columns_in_timeline_step_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = "timeline_step_object";
        $this->addColumn($table, 'timeline_id', $this->integer()->comment("[СВЯЗЬ] c таймлайном"));
        $this->createIndex(
            'idx-timeline_step_object-timeline_id',
            'timeline_step_object',
            'timeline_id'
        );

        $this->addForeignKey(
            'fk-timeline_step_object-timeline_id',
            'timeline_step_object',
            'timeline_id',
            'timeline',
            'id',
            'CASCADE'
        );

        $this->addColumn($table, 'class_name', $this->string()->comment("класс объекта"));
        $this->addColumn($table, 'deal_type_name', $this->string()->comment("тип сделки"));
        $this->addColumn($table, 'visual_id', $this->string()->comment("Визуальный ID предложения"));
        $this->addColumn($table, 'address', $this->string()->comment("Адрес объекта"));
        $this->addColumn($table, 'area', $this->string());
        $this->addColumn($table, 'price', $this->string());
        $this->addColumn($table, 'image', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220901_122938_add_columns_in_timeline_step_object_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220901_122938_add_columns_in_timeline_step_object_table cannot be reverted.\n";

        return false;
    }
    */
}
