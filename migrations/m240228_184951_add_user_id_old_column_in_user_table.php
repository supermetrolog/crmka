<?php

use app\models\oldDb\User as OldUser;
use app\models\User\User;
use yii\db\Migration;

/**
 * Class m240228_184951_add_user_id_old_column_in_user_table
 */
class m240228_184951_add_user_id_old_column_in_user_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn(
			'user',
			'user_id_old',
			$this->integer()->null()
		);

		$this->createIndex(
			'user-user_id_old',
			'user',
			'user_id_old',
			true
		);

		/** @var OldUser[] $oldUsers */
		$oldUsers = OldUser::find()->andWhere(['IS NOT', 'user_id_new', null])->all();

		foreach ($oldUsers as $oldUser) {
			/** @var User $user */
			$user              = User::find()->andWhere(['id' => $oldUser->user_id_new])->one();
			$user->user_id_old = $oldUser->id;
			$user->save(false);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropColumn('user', 'user_id_old');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m240228_184951_add_user_id_old_column_in_user_table cannot be reverted.\n";

		return false;
	}
	*/
}
