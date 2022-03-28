<?php

namespace app\controllers;

use yii\rest\ActiveController;
use Yii;
use app\models\Contact;
use app\models\miniModels\ContactComment;
use yii\web\NotFoundHttpException;
use app\behaviors\BaseControllerBehaviors;
use app\models\ContactSearch;

class ContactController extends ActiveController
{
    public $modelClass = 'app\models\Contact';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index', 'view', '*']);
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }
    public function actionIndex()
    {
        $searchModel = new ContactSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
    public function actionCompanyContacts($id)
    {
        return Contact::getCompanyContactList($id);
    }
    public function actionView($id)
    {
        return  Contact::find()->with(['emails', 'phones', 'websites', 'wayOfInformings', 'consultant.userProfile', 'contactComments.author.userProfile'])
            ->where(['id' => $id])
            ->limit(1)->one();
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
