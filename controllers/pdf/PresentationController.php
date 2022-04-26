<?php

namespace app\controllers\pdf;

use app\models\pdf\Pdf;
use yii\web\Controller;
use app\behaviors\BaseControllerBehaviors;

class PresentationController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['*']);
    }

    public function actionIndex()
    {
        return true;
    }
    public function actionFuck()
    {
        return true;
    }
}
