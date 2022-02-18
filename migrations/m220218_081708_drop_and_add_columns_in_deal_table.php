<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_add_columns_in_deal}}`.
 */
class m220218_081708_drop_and_add_columns_in_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('deal', 'startEventTime');
        $this->dropColumn('deal', 'endEventTime');
        $this->addColumn('deal', 'dealDate', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('дата сделки'));
        $this->addColumn('deal', 'contractTerm', $this->integer()->defaultValue(null)->comment('срок контракта'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
