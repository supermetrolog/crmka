<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%notNull_index_for_maxCeilingHeight_column_in_request}}`.
 */
class m220527_143519_drop_notNull_index_for_maxCeilingHeight_column_in_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('request', 'maxCeilingHeight', $this->integer()->comment('Максимальная высота потолков'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
