<?php

use yii\db\Migration;

/**
 * Class m210922_143552_add_complex_id_column_in_timeline_step_object_table
 */
class m210922_143552_add_complex_id_column_in_timeline_step_object_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_step_object', 'complex_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210922_143552_add_complex_id_column_in_timeline_step_object_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210922_143552_add_complex_id_column_in_timeline_step_object_table cannot be reverted.\n";

        return false;
    }
    */
}
