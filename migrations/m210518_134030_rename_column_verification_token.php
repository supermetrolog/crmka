<?php

use yii\db\Migration;

/**
 * Class m210518_134030_rename_column_verification_token
 */
class m210518_134030_rename_column_verification_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%user}}', 'verification_token','access_token');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210518_134030_rename_column_verification_token cannot be reverted.\n";

        return false;
    }
}
