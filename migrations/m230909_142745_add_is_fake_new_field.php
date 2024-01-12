<?php

use yii\db\Migration;

/**
 * Class m230909_142745_add_is_fake_new_field
 */
class m230909_142745_add_is_fake_new_field extends Migration
{
    public function init()
    {
        parent::init();
        $this->db = Yii::$app->db_old;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (YII_ENV !== 'staging') {
            $this->addColumn(
                'c_industry_blocks',
                'is_fake_new',
                $this->tinyInteger(1)->notNull()->defaultValue(0)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230909_142745_add_is_fake_new_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230909_142745_add_is_fake_new_field cannot be reverted.\n";

        return false;
    }
    */
}
