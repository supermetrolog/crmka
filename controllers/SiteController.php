<?php

namespace app\controllers;

use app\components\avito\AvitoFeedGenerator;
use app\components\connector\avito\AvitoConnector;
use app\models\OfferMix;
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
    public function actionIndex()
    {
        $this->response->format = Response::FORMAT_XML;

        $avitoFeedGenerator = new AvitoFeedGenerator();
        $models = OfferMix::find()
            ->limit(5)
            ->notDelete()
            ->active()
            ->offersType()
            ->all();

        $connector = new AvitoConnector($models);

        $avitoFeedGenerator->setAvitoObjects($connector->getData());

        $res = $avitoFeedGenerator->generate();

        $this->response->content = $res;
    }
}
