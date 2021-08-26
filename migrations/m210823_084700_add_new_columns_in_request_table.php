<?php

use yii\db\Migration;

/**
 * Class m210823_084700_add_new_columns_in_request_table
 */
class m210823_084700_add_new_columns_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'movingDate', $this->timestamp()->defaultValue(null)->comment('Дата переезда'));
        $this->addColumn('request', 'unknownMovingDate', $this->integer(2)->defaultValue(null)->comment('[флаг] Нет конкретики по сроку переезда/рассматривает постоянно'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210823_084700_add_new_columns_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210823_084700_add_new_columns_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
