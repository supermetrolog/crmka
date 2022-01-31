<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use app\behaviors\BaseControllerBehaviors;

class CompanygroupController extends ActiveController
{
    public $modelClass = 'app\models\Companygroup';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, []);
    }
}
