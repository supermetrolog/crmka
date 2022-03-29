<?php

namespace app\controllers;

use app\models\Timeline;
use app\models\miniModels\TimelineStep;
use app\models\miniModels\TimelineStepObject;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use app\behaviors\BaseControllerBehaviors;
use app\events\SendMessageEvent;
use app\models\miniModels\TimelineActionComment;
use app\models\UserSendedData;
use Yii;

/**
 * RequestController implements the CRUD actions for Request model.
 */
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
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, []);
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
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

    public function actionSendObjects()
    {
        $post_data = Yii::$app->request->post();
        $stepName = TimelineStep::STEPS[$post_data['step']];
        $this->trigger(self::SEND_OBJECTS_EVENT, new SendMessageEvent([
            'user_id' => Yii::$app->user->identity->id,
            'htmlBody' => '<b>Fucking objects sended</b>',
            'subject' => 'Objects',
            'contacts' => $post_data['contacts'],
            'wayOfSending' => $post_data['wayOfSending'],
            'type' => UserSendedData::OBJECTS_SEND_FROM_TIMELINE_TYPE,
            'description' => 'Отправил объекты на шаге "' . $stepName . '"',
        ]));
        return ['message' => 'Объекты отправлены!', 'data' => true];
    }
    protected function findModel($id)
    {
        if (($model = Timeline::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
