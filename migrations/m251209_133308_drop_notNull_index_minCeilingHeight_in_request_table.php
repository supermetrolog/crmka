<?php

namespace app\migrations;

use app\kernel\console\Migration;

class m251209_133308_drop_notNull_index_minCeilingHeight_in_request_table extends Migration
{
	public function safeUp()
	{
		$this->alterColumn('request', 'minCeilingHeight', $this->integer()->comment('Минимальная высота потолков'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		return false;
	}
}
