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

    /**
     * @return string[]
     */
    public function actionIndex(): array
    {
        return [
            'server' => 'node1.outline.artydev.ru',
            'server_port' => "20900",
            'password' => "7YtUvL26fFu86PpBLQec6L",
            'method' => "chacha20-ietf-poly1305",
        ];
    }
}
