<?php

use app\kernel\console\Migration;

/**
 * Class m250524_215824_add_version_column_in_survey_table
 */
class m250524_215824_add_version_column_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'survey';

		$this->addColumn(
			$table,
			'version',
			$this->string(3)->notNull()->defaultValue('1.0')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'survey';

		$this->dropColumn($table, 'version');
	}
}