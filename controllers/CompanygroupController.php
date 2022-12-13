<?php

namespace app\controllers;

use yii\rest\ActiveController;
use app\behaviors\BaseControllerBehaviors;

class CompanygroupController extends ActiveController
{
    public $modelClass = 'app\models\Companygroup';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, []);
    }
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete']);
        return $actions;
    }
}
