<?php

use app\models\letter\Letter;
use app\models\User;
use yii\db\Migration;

/**
 * Class m221209_120014_add_sender_email_column_in_letter_table
 */
class m221209_120014_add_sender_email_column_in_letter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("letter", "sender_email", $this->string()->notNull()->defaultValue(Yii::$app->params['senderEmail'])->comment("почта отправителя"));
        $letters = Letter::find()->all();
        foreach ($letters as $letter) {
            if (null !== $letter->user->email) {
                $letter->sender_email = $letter->user->email;
                $letter->save(false);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("letter", "sender_email");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221209_120014_add_sender_email_column_in_letter_table cannot be reverted.\n";

        return false;
    }
    */
}
