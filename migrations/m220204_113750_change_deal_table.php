<?php

use yii\db\Migration;

/**
 * Class m220204_113750_change_deal_table
 */
class m220204_113750_change_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('deal', 'complex_id', $this->integer()->notNull()->comment('ID комплекса'));
        $this->alterColumn('deal', 'object_id', $this->integer()->notNull()->comment('ID предложения'));
        $this->addColumn('deal', 'type_id', $this->integer()->notNull()->comment('Тип предложения'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220204_113750_change_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220204_113750_change_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
