<?php

namespace app\commands;

use Supermetrolog\Synchronizer\services\sync\Synchronizer;
use Yii;
use yii\console\Controller;

class SynchronizerController extends Controller
{

    public function actionThisProject()
    {
        $synchronizer = Yii::$container->get(Synchronizer::class, Yii::$app->params['synchronizer']['this_project']);
        $synchronizer->load();
        $synchronizer->sync();
    }

    public function actionObjectsProject()
    {
        $synchronizer = Yii::$container->get(Synchronizer::class, Yii::$app->params['synchronizer']['objects_project']);
        $synchronizer->load();
        $synchronizer->sync();
    }

    public function actionFrontendProject()
    {
        $synchronizer = Yii::$container->get(Synchronizer::class, Yii::$app->params['synchronizer']['frontend_project']);
        $synchronizer->load();
        $synchronizer->sync();
    }
}
