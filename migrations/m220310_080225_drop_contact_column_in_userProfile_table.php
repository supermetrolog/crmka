<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%contact_column_in_userProfile}}`.
 */
class m220310_080225_drop_contact_column_in_userProfile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('user_profile', 'contacts');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
