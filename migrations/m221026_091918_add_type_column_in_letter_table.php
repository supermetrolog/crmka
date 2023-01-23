<?php

use yii\db\Migration;

/**
 * Class m221026_091918_add_type_column_in_letter_table
 */
class m221026_091918_add_type_column_in_letter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('letter', 'type', $this->tinyInteger()->notNull()->comment("Отправлено из таймлайна или другим способом"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221026_091918_add_type_column_in_letter_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221026_091918_add_type_column_in_letter_table cannot be reverted.\n";

        return false;
    }
    */
}
