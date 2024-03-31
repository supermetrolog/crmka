<?php

namespace app\controllers;

use app\models\ChatMember;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function actionIndex(): void
    {
	    $members = ChatMember::find()->orderBy(['id' => SORT_DESC])->with(['offerMix', 'user'])->all();

	    foreach ($members as $member) {
		    dump($member->id, $member->model_id, $member->model_type, $member->model);
//		    dump($member->id, $member->model_type, $member->model->id, get_class($member->model));
	    }

		dd('ANAL');
    }
}
