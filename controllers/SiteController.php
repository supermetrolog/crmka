<?php

namespace app\controllers;

use app\models\Company;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Login;
use app\models\ContactForm;
use app\events\NotificationEvent;
use app\events\SendMessageEvent;
use app\models\User;
use app\models\UserSendedData;

class SiteController extends Controller
{
    public const FUCK_EVENT = 'fuck_event';
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

    public function init()
    {
        $this->on(self::FUCK_EVENT, [Yii::$app->notify, 'sendMessage']);
        parent::init();
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $contacts = ['+7 (966) 555-12-58', 'fuck@mail.ru'];
        // $this->trigger(self::FUCK_EVENT, new SendMessageEvent([
        //     'user_id' => 3,
        //     'htmlBody' => '<b>fucking html body</b>',
        //     'subject' => 'tema',
        //     'contacts' => $contacts,
        //     'type' => UserSendedData::OBJECTS_SEND_FROM_TIMELINE_TYPE,
        //     'description' => "<p>Отправил объекты: <a href='http://localhost:8080/'>5623</a></p>"
        // ]));
        var_dump(Yii::$app->user->identity);
        return 'fuck';
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new Login();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
