<?php

use yii\db\Migration;
use app\models\User;

/**
 * Class m211102_115123_create_admin_user
 */
class m211102_115123_create_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $password = 'admin';
        $username = 'admin';
        $user = new User();
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->username = $username;
        $user->created_at = time();
        $user->updated_at = time();
        $user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211102_115123_create_admin_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211102_115123_create_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
