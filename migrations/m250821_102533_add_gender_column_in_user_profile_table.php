<?php

use app\kernel\console\Migration;

class m250821_102533_add_gender_column_in_user_profile_table extends Migration
{
	public function safeUp()
	{
		$this->addColumn('{{%user_profile}}', 'gender', $this->string(1)->defaultValue('m'));
	}

	public function safeDown()
	{
		$this->dropColumn('{{%user_profile}}', 'gender');
	}
}