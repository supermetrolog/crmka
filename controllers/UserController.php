<?php
namespace app\controllers;

use app\models\SignUp;
use app\models\Login;
use app\models\User;
use yii\rest\ActiveController;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login'],
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
        return $model->getErrors();
    }
    public function actionLogout()
    {
        $model = User::findIdentityByAccessToken(Yii::$app->user->identity->access_token);
        $model->generateAccessToken();
        return $model->save(false);
    }
}
