<?php

use app\models\miniModels\Phone;
use yii\db\Migration;

/**
 * Class m230112_124935_change_phone_numbers_that_start_with_eight
 */
class m230112_124935_change_phone_numbers_that_start_with_eight extends Migration
{
    private function print($phone)
    {
        echo "\n";
        echo "ID: " . $phone->id . " len: " . strlen($phone->phone) . " mblen: " . mb_strlen($phone->phone) . " Phone: " . $phone->phone;
    }

    private function isNeededPhone($phone)
    {
        if (strlen($phone) !== 11) {
            return false;
        }

        $firstSymbol = $phone[0];
        if ($firstSymbol == "8") {
            return true;
        }
        return false;
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            $query = Phone::find()->orderBy(['id' => SORT_ASC]);
            foreach ($query->batch(100) as $phones) {
                foreach ($phones as $phone) {
                    if ($this->isNeededPhone($phone->phone)) {
                        $this->print($phone);
                        $newPhone = mb_substr($phone->phone, 1, mb_strlen($phone->phone) - 1);
                        $phone->phone = "7" . $newPhone;
                        $this->print($phone);
                        if (!$phone->save(false)) {
                            throw new Exception("Save ERROR", 1);
                        }
                    }
                }
            }

            $tx->commit();
        } catch (\Throwable $th) {
            $tx->rollBack();
            throw $th;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230112_124935_change_phone_numbers_that_start_with_eight cannot be reverted.\n";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230112_124935_change_phone_numbers_that_start_with_eight cannot be reverted.\n";

        return false;
    }
    */
}
