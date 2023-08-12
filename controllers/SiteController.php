<?php

namespace app\controllers;

use app\components\avito\AvitoFeedGenerator;
use app\components\connector\avito\AvitoConnector;
use app\models\OfferMix;
use app\services\emailsender\EmailSender;
use Swift_SmtpTransport;
use yii\swiftmailer\Mailer;
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
        $mailer = new Mailer(
            [
//                'htmlLayout' => 'layouts/html',
                // 'useFileTransport' => true,
                'useFileTransport' => false,
                'transport' => [
                    'class' => Swift_SmtpTransport::class,
                    // 'host' => 'mailserver3.realtor.ru',
                    'host' => 'smtp.yandex.com',
                    'port' => 465,
                    'encryption' => 'ssl',
                    'username' => 'tim-a@pennylane.pro',
                    'password' => 'studentjke2h',
                ]
            ]
        );

        $message = $mailer->compose()
                            ->setFrom(['tim-a@pennylane.pro' => 'nigga'])
                            ->setTextBody('TEXT BODY')
                            ->setSubject('test')
                            ->setTo('billypro6@gmail.com');
        $res = $message->send();
        var_dump($res);die;
    }
}
