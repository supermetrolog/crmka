<?php

use app\models\User\User;
use app\models\User\UserProfile;
use yii\db\Migration;

/**
 * Class m220405_082430_add_admin_user_data_in_userProfile_table
 */
class m220405_082430_add_admin_user_data_in_userProfile_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$admin            = User::find()->where(['username' => 'admin'])->limit(1)->one();
		$adminUserProfile = UserProfile::find()->where(['user_id' => $admin->id])->limit(1)->one();
		if ($adminUserProfile) {
			return;
		}

		$model = new UserProfile([
			'user_id'     => $admin->id,
			'first_name'  => 'Admin',
			'middle_name' => 'Admin',
			'last_name'   => 'Admin',
			'caller_id'   => '102'
		]);

		if (!$model->save(false)) {
			throw new Exception('Admin user not save');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		echo "m220405_082430_add_admin_user_data_in_userProfile_table cannot be reverted.\n";

		return false;
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m220405_082430_add_admin_user_data_in_userProfile_table cannot be reverted.\n";

		return false;
	}
	*/
}
