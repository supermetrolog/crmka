<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\filters\Cors;
use app\models\letter\Letter;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use app\models\letter\CreateLetter;
use app\models\letter\LetterSearch;
use app\services\emailsender\EmailSender;
use app\behaviors\BaseControllerBehaviors;
use app\models\SendPresentation;
use app\services\queue\jobs\SendPresentationJob;
use yii\web\BadRequestHttpException;

class LetterController extends ActiveController
{
    public $modelClass = 'app\models\Letter';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index']);
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['*'],
                'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
                'Access-Control-Expose-Headers' => ['X-Pagination-Total-Count', 'X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page', 'Link']
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }
    public function actionIndex()
    {
        $searchModel = new LetterSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
    public function actionView($id)
    {
        return Letter::find()->where(['id' => $id])->with([
            "company",
            "user.userProfile",
            "letterOffers.offer.object",
            "letterWays",
            "letterPhones.contact",
            "letterEmails.contact"
        ])->limit(1)->one();
    }

    public function actionSend()
    {
        if (!Yii::$app->request->post()) {
            throw new BadRequestHttpException("body cannot be empty");
        }
        $tx = Yii::$app->db->beginTransaction();
        try {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $post_data = Yii::$app->request->post();
            $post_data['user_id'] = $user->id;
            $post_data['sender_email'] = $user->email ?? Yii::$app->params['senderEmail'];
            $post_data['type'] = Letter::TYPE_DEFAULT;
            $createLetterModel = new CreateLetter();
            $createLetterModel->create($post_data);

            if ($createLetterModel->letterModel->shipping_method == Letter::SHIPPING_OTHER_METHOD) {
                $tx->commit();
                return ['message' => 'Предложения отправлены!', 'data' => $createLetterModel->letterModel->id];
            }
            $model = new SendPresentation();
            $model->load($this->getDataForSendPresentationModel($createLetterModel), '');
            $q = Yii::$app->queue;
            $q->push(new SendPresentationJob([
                'model' => $model
            ]));
            $tx->commit();
            return ['message' => 'Письмо отправлено!', 'data' => $createLetterModel->letterModel->id];
        } catch (\Throwable $th) {
            $tx->rollBack();
            throw $th;
        }
    }
    private function getDataForSendPresentationModel(CreateLetter $createLetterModel): array
    {
        return [
            'offers' => $createLetterModel->offers,
            'emails' => array_map(function ($elem) {
                return $elem['value'];
            }, $createLetterModel->contacts['emails']),
            'phones' => array_map(function ($elem) {
                return $elem['value'];
            }, $createLetterModel->contacts['phones']),
            'comment' => $createLetterModel->letterModel->body,
            'subject' => $createLetterModel->letterModel->subject,
            'wayOfSending' => $createLetterModel->ways,
            'letter_id' => $createLetterModel->letterModel->id,
            'user_id' => $createLetterModel->letterModel->user_id
        ];
    }
    protected function findModel($id)
    {
        if (($model = Letter::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
