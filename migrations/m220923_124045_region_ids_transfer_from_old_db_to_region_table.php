<?php

use app\models\miniModels\RequestRegion;
use yii\db\Migration;

/**
 * Class m220923_124045_region_ids_transfer_from_old_db_to_region_table
 */
class m220923_124045_region_ids_transfer_from_old_db_to_region_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $array = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            0 => 6,
            6 => 7,
            7 => 8,
            14 => 9,
            9 => 10,
            8 => 11,
            10 => 17,
            11 => 18,
            13 => 19,
            12 => 20,
        ];

        $models = RequestRegion::find()->all();
        foreach ($models as  $model) {
            $model->region = $array[$model->region];
            if (!$model->save(false)) {
                throw new Exception(json_encode($model->getErrorSummary(false)));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220923_124045_region_ids_transfer_from_old_db_to_region_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220923_124045_region_ids_transfer_from_old_db_to_region_table cannot be reverted.\n";

        return false;
    }
    */
}
