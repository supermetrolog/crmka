<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\Cors;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use app\models\Contact;
use app\models\miniModels\ContactComment;
use yii\web\NotFoundHttpException;

class ContactController extends ActiveController
{
    public $modelClass = 'app\models\Contact';

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
            'except' => ['search', 'index', 'options', 'company-contacts', 'create', 'delete', 'update', 'create-comment'],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['delete']);
        unset($actions['update']);
        return $actions;
    }
    public function actionCompanyContacts($id)
    {
        return Contact::getCompanyContactList($id);
    }
    public function actionCreate()
    {
        return Contact::createContact(Yii::$app->request->post());
    }
    public function actionUpdate($id)
    {
        return Contact::updateContact($this->findModel($id), Yii::$app->request->post());
    }
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return ['message' => 'Контакт удален', 'data' => true];
    }
    public function actionCreateComment()
    {
        return ContactComment::createComment(Yii::$app->request->post());
    }
    // public function actionSearch()
    // {
    //     $search = new CompanySearch();
    //     $searchByAttr['CompanySearch'] = Yii::$app->request->queryParams;
    //     return $search->search($searchByAttr);
    // }
    protected function findModel($id)
    {
        if (($model = Contact::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
