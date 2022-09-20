<?php

namespace app\controllers;

use app\models\Timeline;
use app\models\miniModels\TimelineStep;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use app\behaviors\BaseControllerBehaviors;
use app\components\NotificationService;
use app\events\SendMessageEvent;
use app\models\miniModels\TimelineActionComment;
use app\models\pdf\OffersPdf;
use app\models\pdf\PdfManager;
use app\models\SendPresentation;
use app\models\User;
use app\models\UserSendedData;
use app\services\pythonpdfcompress\PythonPdfCompress;
use app\services\queue\jobs\SendPresentationJob;
use Dompdf\Options;
use Exception;
use Yii;
use yii\web\BadRequestHttpException;

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
    public function actionSendObjects()
    {
        if (!Yii::$app->request->post()) {
            throw new BadRequestHttpException("body cannot be empty");
        }
        $post_data = Yii::$app->request->post();
        $post_data['user_id'] = Yii::$app->user->identity->id;
        $post_data['type'] =  UserSendedData::OBJECTS_SEND_FROM_TIMELINE_TYPE;
        $post_data['description'] = 'Отправил объекты на шаге "' . TimelineStep::STEPS[1] . '"';

        $model = new SendPresentation();
        $model->load($post_data, '');
        $q = Yii::$app->queue;
        $q->push(new SendPresentationJob([
            'model' => $model
        ]));
        return ['message' => 'Предложения отправлены!', 'data' => true];
    }
    protected function findModel($id)
    {
        if (($model = Timeline::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
