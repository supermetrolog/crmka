<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\forms\Letter\SendLetterForm;
use app\models\letter\CreateLetter;
use app\models\letter\Letter;
use app\models\letter\LetterSearch;
use app\models\SendPresentation;
use app\resources\Letter\LetterResource;
use app\services\queue\jobs\SendPresentationJob;
use app\usecases\Letter\LetterService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class LetterController extends AppController
{
	private LetterService $letterService;

	public function __construct(
		$id,
		$module,
		LetterService $letterService,
		array $config = []
	)
	{
		$this->letterService = $letterService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		return (new LetterSearch())->search($this->request->get());
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

	/**
	 * @throws BadRequestHttpException
	 * @throws Exception
	 * @throws ValidationErrorHttpException
	 * @throws Throwable
	 */
	public function actionSend(): array
	{
		// TODO: Refactor me

		$post = $this->request->post();

		if (!$post) {
			throw new BadRequestHttpException("Body cannot be empty");
		}

		$tx = Yii::$app->db->beginTransaction();

		try {
			$user = $this->user->identity;

			$dto = [
				...$post,
				'user_id'      => $user->id,
				'sender_email' => $user->email ?? Yii::$app->params['senderEmail'],
				'type'         => Letter::TYPE_DEFAULT
			];

			$createLetterModel = new CreateLetter();
			$createLetterModel->create($dto);

			if ($createLetterModel->letterModel->shipping_method == Letter::SHIPPING_OTHER_METHOD) {
				$tx->commit();

				return ['message' => 'Предложения отправлены!', 'data' => $createLetterModel->letterModel->id];
			}

			$model = new SendPresentation();

			$model->load($this->getDataForSendPresentationModel($createLetterModel), '');

			$q = Yii::$app->queue;

			$q->push(
				new SendPresentationJob([
					'model' => $model
				])
			);

			$tx->commit();

			return ['message' => 'Письмо отправлено!', 'data' => $createLetterModel->letterModel->id];
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	private function getDataForSendPresentationModel(CreateLetter $createLetterModel): array
	{
		return [
			'offers'       => $createLetterModel->offers,
			'emails'       => array_map(function ($elem) {
				return $elem['value'];
			}, $createLetterModel->contacts['emails']),
			'phones'       => array_map(function ($elem) {
				return $elem['value'];
			}, $createLetterModel->contacts['phones']),
			'comment'      => $createLetterModel->letterModel->body,
			'subject'      => $createLetterModel->letterModel->subject,
			'wayOfSending' => $createLetterModel->ways,
			'letter_id'    => $createLetterModel->letterModel->id,
			'user_id'      => $createLetterModel->letterModel->user_id
		];
	}

	/**
	 * @throws BadRequestHttpException
	 * @throws Exception
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 */
	public function actionSendCustomLetter(): LetterResource
	{
		$form = new SendLetterForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$user = $this->user->identity;

		$dto = $form->getDto($user->id, $user->email ?? Yii::$app->params['senderEmail']);

		$letter = $this->letterService->send($dto);

		return new LetterResource($letter);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel($id): Letter
	{
		if (($model = Letter::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
