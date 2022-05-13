<?php

use yii\db\Migration;

/**
 * Class m220513_102814_alter_deal_table
 */
class m220513_102814_alter_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('deal', 'original_id', $this->integer()->notNull()->comment('[СВЯЗЬ] с offers в базе объектов'));
        $this->createIndex('idx-deal-original_id', 'deal', 'original_id');
        $this->alterColumn('deal', 'object_id', $this->integer()->notNull()->comment('[СВЯЗЬ] с objects в базе объектов'));
        $this->createIndex('idx-deal-object_id', 'deal', 'object_id');
        $this->createIndex('idx-deal-type_id', 'deal', 'type_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220513_102814_alter_deal_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220513_102814_alter_deal_table cannot be reverted.\n";

        return false;
    }
    */
}
