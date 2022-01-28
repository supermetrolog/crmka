<?php

use yii\db\Migration;

/**
 * Class m220128_122937_add_passive_why_columns_in_request_table
 */
class m220128_122937_add_passive_why_columns_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('request', 'passive_why', $this->tinyInteger()->defaultValue(null)->comment('Причина пассива запроса'));
        $this->addColumn('request', 'passive_why_comment', $this->string()->defaultValue(null)->comment('Комментарий почему запрос пассив'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220128_122937_add_passive_why_columns_in_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220128_122937_add_passive_why_columns_in_request_table cannot be reverted.\n";

        return false;
    }
    */
}
