<?php

namespace app\controllers;

use app\models\SignUp;
use yii\filters\Cors;
use app\models\Login;
use app\models\User;
use yii\rest\ActiveController;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use app\exceptions\ValidationErrorHttpException;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['*'],
                'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login', 'create', 'index', 'options'],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }
    public function actionCreate()
    {
        $model = new SignUp();
        if ($model->load(Yii::$app->request->post(), '')) {
            return $model->signUp();
        }
        return $model->getErrors();
    }
    public function actionLogin()
    {
        $model = new Login();
        if ($model->load(Yii::$app->request->post(), '')) {
            return $model->login();
        }
        throw new ValidationErrorHttpException($model->getErrorSummary(false));
    }
    public function actionLogout()
    {
        $model = User::findIdentityByAccessToken(Yii::$app->user->identity->access_token);
        $model->generateAccessToken();
        return ['message' => 'Вы вышли из аккаунта', 'data' => $model->save(false)];
    }
}
