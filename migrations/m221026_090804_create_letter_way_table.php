<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%letter_way}}`.
 */
class m221026_090804_create_letter_way_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%letter_way}}', [
            'id' => $this->primaryKey(),
            'letter_id' => $this->integer()->notNull()->comment("[СВЯЗЬ] с отправленными письмами"),
            'way' => $this->smallInteger()->notNull()->comment("Каким способом отправлено письмо")
        ]);
        $this->createIndex(
            'idx-letter_way-letter_id',
            'letter_way',
            'letter_id'
        );

        $this->addForeignKey(
            'fk-letter_way-letter_id',
            'letter_way',
            'letter_id',
            'letter',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%letter_way}}');
    }
}
