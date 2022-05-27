<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%notNull_index_for_heated_column_in_request}}`.
 */
class m220527_123600_drop_notNull_index_for_heated_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('request', 'heated', $this->tinyInteger(1)->comment('[ФЛАГ] Отапливаемый или нет'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
