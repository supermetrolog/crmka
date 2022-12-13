<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\models\miniModels\TimelineStep;
use app\models\SendPresentation;
use app\models\User;
use app\models\UserSendedData;
use app\services\emailsender\EmailSender;
use app\services\pythonpdfcompress\PythonPdfCompress;
use app\services\queue\jobs\SendPresentationJob;
use app\services\queue\jobs\TestJob;
use Yii;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
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
        return "fuck";
    }
}
