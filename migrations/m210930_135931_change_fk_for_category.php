<?php

use yii\db\Migration;

/**
 * Class m210930_135931_change_fk_for_category
 */
class m210930_135931_change_fk_for_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-category-company_id', 'category');
        $this->addForeignKey(
            'fk-category-company_id',
            'category',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210930_135931_change_fk_for_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210930_135931_change_fk_for_category cannot be reverted.\n";

        return false;
    }
    */
}
