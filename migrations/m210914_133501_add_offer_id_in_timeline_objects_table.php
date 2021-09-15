<?php

use yii\db\Migration;

/**
 * Class m210914_133501_add_offer_id_in_timeline_objects_table
 */
class m210914_133501_add_offer_id_in_timeline_objects_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timeline_step_object', 'offer_id', $this->integer()->notNull()->comment('Нужен для поиска сразу нескольких предложений по API'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210914_133501_add_offer_id_in_timeline_objects_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210914_133501_add_offer_id_in_timeline_objects_table cannot be reverted.\n";

        return false;
    }
    */
}
