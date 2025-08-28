<?php

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use yii\rest\ActiveController;

class CompanygroupController extends ActiveController
{
	public $modelClass = 'app\models\Company\Companygroup';

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
