<?php

use yii\db\Migration;

/**
 * Class m210729_081931_delete_timeline_tables
 */
class m210729_081931_delete_timeline_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('timeline_object');
        $this->dropTable('feedback_way');
        $this->dropTable('timeline_action');
        $this->dropTable('timeline');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210729_081931_delete_timeline_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210729_081931_delete_timeline_tables cannot be reverted.\n";

        return false;
    }
    */
}
