<?php

use yii\db\Migration;

/**
 * Class m221026_133501_add_shipping_method_column_in_letter_table
 */
class m221026_133501_add_shipping_method_column_in_letter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('letter', 'shipping_method', $this->tinyInteger()->notNull()->defaultValue(1)->comment("1 - через систему, 0 - другими методами"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221026_133501_add_shipping_method_column_in_letter_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221026_133501_add_shipping_method_column_in_letter_table cannot be reverted.\n";

        return false;
    }
    */
}
