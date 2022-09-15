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
    // public function actionIndex()
    // {
    //     $testPostData = [
    //         'emails' => ["billypro6@gmail.com", "billypro6@gmail.com"],
    //         'from' => ['tim-a@realtor.ru' => "test"],
    //         'view' => 'presentation/index',
    //         'viewArgv' => ['userMessage' => "comment"],
    //         'subject' => 'Список предложений от Pennylane Realty',
    //         // 'username' => "tim-a",
    //         // 'password' => 'Vd$sor2'
    //     ];

    //     $testPostData['user_id'] = 3;
    //     $model = new EmailSender();
    //     $model->load($testPostData, '');
    //     $model->validate();
    //     // if (!$model->hasErrors()) {
    //     //     $model->send();
    //     // }
    //     var_dump($model->getErrorSummary(false));
    // }
    public function actionIndex()
    {
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
            'sendClientFlag' => true,
            'step' => 1,
            'wayOfSending' => [0],
            'type' => UserSendedData::OBJECTS_SEND_FROM_TIMELINE_TYPE,
            'description' => 'Отправил объекты на шаге "' . TimelineStep::STEPS[1] . '"',
        ];

        $testPostData['user_id'] = 3;
        $model = new SendPresentation();
        $model->load($testPostData, '');
        $q = Yii::$app->queue;
        $q->push(new SendPresentationJob([
            'model' => $model
        ]));
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
