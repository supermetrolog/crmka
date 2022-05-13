<?php

use yii\db\Migration;

/**
 * Class m220513_120343_add_visual_id_column_in_deal_table
 */
class m220513_120343_add_visual_id_column_in_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('deal', 'visual_id', $this->string()->notNull()->comment('Визульный айди предложения из базы объектов'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220513_120343_add_visual_id_column_in_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220513_120343_add_visual_id_column_in_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
