<?php

namespace app\controllers;

use app\models\Timeline;
use app\models\miniModels\TimelineStep;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use app\behaviors\BaseControllerBehaviors;
use app\models\letter\CreateLetter;
use app\models\letter\Letter;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\miniModels\TimelineActionComment;
use app\models\SendPresentation;
use app\models\timeline\AddActionComments;
use app\services\queue\jobs\SendPresentationJob;
use Yii;
use yii\web\BadRequestHttpException;

class TimelineController extends ActiveController
{
    public $modelClass = 'app\models\Timeline';

    public const SEND_OBJECTS_EVENT = 'send_objects';
    public function init()
    {
        $this->on(self::SEND_OBJECTS_EVENT, [Yii::$app->notify, 'sendMessage']);
        parent::init();
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index']);
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }
    public function actionUpdate($id)
    {
        return $id;
    }
    public function actionUpdateStep($id)
    {
        return TimelineStep::updateTimelineStep($id, Yii::$app->request->post());
    }
    public function actionIndex()
    {
        $consultant_id = Yii::$app->request->getQueryParam('consultant_id');
        $request_id = Yii::$app->request->getQueryParam('request_id');
        return Timeline::getTimeline($consultant_id, $request_id);
    }
    public function actionActionComments($id)
    {
        return TimelineActionComment::getTimelineComments($id);
    }
    public function actionAddActionComments()
    {
        $addActionComment = new AddActionComments(Yii::$app->request->post());
        $addActionComment->add();
        return [
            'data' => true,
            'message' => "Комментарий добавлен",
        ];
    }
    protected function findModel($id)
    {
        if (($model = Timeline::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
