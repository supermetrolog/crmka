<?php

declare(strict_types=1);

namespace app\commands;

use app\models\ChatMember;
use yii\console\Controller;

class TestController extends Controller
{

	public function actionIndex(): void
	{
		$members = ChatMember::find()->orderBy(['id' => SORT_DESC])->all();

		foreach ($members as $member) {
			dump($member->id, $member->model_type, $member->model->id, get_class($member->model));
		}
	}
}