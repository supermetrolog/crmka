<?php

namespace app\controllers;

use app\models\Login;
use app\models\User;
use yii\rest\ActiveController;
use Yii;
use app\exceptions\ValidationErrorHttpException;
use yii\web\UploadedFile;
use app\models\UploadFile;
use yii\web\NotFoundHttpException;
use app\behaviors\BaseControllerBehaviors;
use yii\filters\AccessControl;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {

        $behaviors = parent::behaviors();
        $exceptActions = ['login', 'logout'];
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, $exceptActions);
        // $behaviors['access'] = [
        //     'class' => AccessControl::class,
        //     'rules' => [
        //         [
        //             'allow' => true,
        //             'actions' => ['index', 'create', 'update'],
        //             'roles' => ['admin']
        //         ],
        //         [
        //             'allow' => true,
        //             'actions' => $exceptActions,
        //             'roles' => ['?']
        //         ],
        //         [
        //             'allow' => true,
        //             'actions' => ['view'],
        //             'roles' => ['@']
        //         ],
        //     ]
        // ];
        return $behaviors;
    }
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['index']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }
    public function actionIndex()
    {
        return User::getUsers();
    }
    public function actionView($id)
    {
        return User::getUser($id);
    }
    public function actionCreate()
    {
        $request = json_decode(Yii::$app->request->post('data'), true);
        $model = new UploadFile();
        $model->files = UploadedFile::getInstancesByName('files');
        return User::createUser($request, $model);
    }
    public function actionUpdate($id)
    {
        $request = json_decode(Yii::$app->request->post('data'), true);
        $model = new UploadFile();
        $model->files = UploadedFile::getInstancesByName('files');
        return User::updateUser($this->findModel($id), $request, $model);
    }
    // public function actionDelete($id)
    // {
    //     $model = $this->findModel($id);
    //     $model->status = User::STATUS_DELETED;
    //     if ($model->save()) {
    //         return ['message' => "Пользователь удален", 'data' => $id];
    //     }
    //     throw new ValidationErrorHttpException($model->getErrorSummary(false));
    // }
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
            return ['message' => 'Вы вышли из системы'];
        }
        $model = User::findIdentityByAccessToken(Yii::$app->user->identity->access_token);
        $model->generateAccessToken();
        return ['message' => 'Вы вышли из аккаунта', 'data' => $model->save(false)];
    }
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
