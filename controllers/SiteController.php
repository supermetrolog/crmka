<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use PhpAmqpLib\Message\AMQPMessage;
use app\components\NotificationsQueueService;

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
        /** @var NotificationsQueueService */
        $notifyQueue = Yii::$app->notifyQueue;

        $toSend = new AMQPMessage("SUKA NAHUI");
        $notifyQueue->publish($toSend);
    }
}
