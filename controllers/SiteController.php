<?php

namespace app\controllers;

use Psr\Log\LoggerInterface;
use Yii;
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
    public function dump($var): void
    {
        echo "<pre>";
        print_r($var);
    }
    public function actionIndex()
    {
        $one = require __DIR__ . "/../config/dev/web/config.php";
        $two = require __DIR__ . "/../config/common/web/config.php";

        $res = array_merge_recursive($two, $one);
        unset($res['container']);
        // $this->dump(array_merge_recursive($one, $two));
        $this->dump($res);
        // $this->dump($two);
    }
}
