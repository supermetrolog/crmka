<?php
namespace app\controllers;

use app\models\SignUp;
use app\models\Login;
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
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }
    public function actionFuck()
    {
        return [
            'anal' => 'hole',
            'penis' => 'stick'
        ];
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
}
