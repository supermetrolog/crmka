<?php

use yii\db\Migration;

/**
 * Class m230905_202117_add_is_fake_column_in_offers_mix_table_and_blocks_table
 */
class m230905_202117_add_is_fake_column_in_offers_mix_table_and_blocks_table extends Migration
{
    public function init()
    {
        $this->db = Yii::$app->db_old;
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'c_industry_offers_mix',
            'is_fake',
            $this->boolean()->notNull()->defaultValue(false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('c_industry_offers_mix', 'is_fake');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230905_202117_add_is_fake_column_in_offers_mix_table_and_blocks_table cannot be reverted.\n";

        return false;
    }
    */
}
