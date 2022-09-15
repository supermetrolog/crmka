<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\models\SendPresentation;
use app\models\User;
use app\services\pythonpdfcompress\PythonPdfCompress;
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
        // $q = Yii::$app->queue;
        // $q->push(new TestJob([
        //     'text' => "Fuck the police"
        // ]));


        $testPostData = [
            'comment' => "fuck",
            'contacts' => [
                'fuck@gmail.com'
            ],
            'offers' => [
                [
                    'object_id' => 10377,
                    'type_id' => 2,
                    'original_id' => 2938,
                    'consultant' => "TIMUR"
                ]
            ],
            'sendClientFlag' => false,
            'step' => 1,
            'wayOfSending' => [0]
        ];

        $model = new SendPresentation();
        $model->load($testPostData, '');
        $model->validate();

        if ($model->hasErrors()) {
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
        return 'fuck';
    }
    // public function actionIndex()
    // {
    //     // $appPath = Yii::getAlias("@app");
    //     $pyScriptPath = "C:\Users\\tim-a\Desktop\pdfcompressor\pdf_compressor.py";
    //     $inpath = "C:\Users\\tim-a\Desktop\presentation_218_rent.pdf";
    //     $outpath = "C:\Users\\tim-a\Desktop\presentation_218_rent_compressed.pdf";
    //     $pythonpath = "C:\Python310\python.exe";
    //     $pythonCompresser = new PythonPdfCompress($pythonpath, $pyScriptPath, $inpath, $outpath);
    //     $pythonCompresser->Compress();
    //     $pythonCompresser->deleteOriginalFileAndChangeFileName();
    //     return "fuck";
    // }
}
