<?php

use yii\db\Migration;

/**
 * Class m210824_112240_update_index_for_request_prefix_tables
 */
class m210824_112240_update_index_for_request_prefix_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-request_region-request_id', 'request_region');
        $this->dropForeignKey('fk-request_object_class-request_id', 'request_object_class');
        $this->dropForeignKey('fk-request_gate_type-request_id', 'request_gate_type');
        $this->dropForeignKey('fk-request_object_type-request_id', 'request_object_type');
        $this->dropForeignKey('fk-request_direction-request_id', 'request_direction');
        $this->dropForeignKey('fk-request_district-request_id', 'request_district');

        $this->addForeignKey(
            'fk-request_region-request_id',
            'request_region',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-request_object_class-request_id',
            'request_object_class',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-request_gate_type-request_id',
            'request_gate_type',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-request_object_type-request_id',
            'request_object_type',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-request_direction-request_id',
            'request_direction',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-request_district-request_id',
            'request_district',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210824_112240_update_index_for_request_prefix_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210824_112240_update_index_for_request_prefix_tables cannot be reverted.\n";

        return false;
    }
    */
}
