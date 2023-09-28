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
        $this->response->format = Response::FORMAT_JSON;

        return [
            'server' => '45.144.234.134',
            'server_port' => "54632",
            'password' => "FfV3K21f0g0az9RwRTRiG4",
            'method' => "chacha20-ietf-poly1305",
        ];
    }
}
