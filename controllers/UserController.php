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
use yii\web\UploadedFile;
use app\models\UploadFile;

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
            'except' => ['login', 'logout', 'create', 'index', 'options'],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['index']);
        return $actions;
    }
    public function actionIndex()
    {
        return User::getUsers();
    }
    public function actionCreate()
    {
        $request = json_decode(Yii::$app->request->post('data'), true);
        $model = new UploadFile();
        $model->files = UploadedFile::getInstancesByName('files');
        return User::createUser($request, $model);
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
        if (Yii::$app->user->isGuest) {
            return ['message' => 'Вы не авторизованны'];
        }
        $model = User::findIdentityByAccessToken(Yii::$app->user->identity->access_token);
        $model->generateAccessToken();
        return ['message' => 'Вы вышли из аккаунта', 'data' => $model->save(false)];
    }
}
