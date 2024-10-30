<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Contact;
use app\models\ContactSearch;
use app\models\forms\Contact\ContactCommentForm;
use app\models\forms\Contact\ContactForm;
use app\repositories\ContactRepository;
use app\resources\Contact\Comment\ContactCommentResource;
use app\resources\Contact\ContactResource;
use app\resources\Contact\ContactWithCommentsResource;
use app\usecases\Contact\ContactCommentService;
use app\usecases\Contact\ContactService;
use ErrorException;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ContactController extends AppController
{
	private ContactService        $contactService;
	private ContactCommentService $contactCommentService;
	private ContactRepository     $repository;

	public function __construct(
		$id,
		$module,
		ContactService $contactService,
		ContactCommentService $contactCommentService,
		ContactRepository $repository,
		array $config = []
	)
	{
		$this->contactService        = $contactService;
		$this->contactCommentService = $contactCommentService;
		$this->repository            = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new ContactSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return ContactResource::fromDataProvider($dataProvider);
	}

	public function actionCompanyContacts($id): array
	{
		$resource = $this->repository->findAllByCompanyId($id);

		return ContactWithCommentsResource::collection($resource);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView($id): ContactWithCommentsResource
	{
		return new ContactWithCommentsResource($this->findModel($id));
	}

	/**
	 * @return ContactResource
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 * @throws ValidateException
	 */
	public function actionCreate(): ContactResource
	{
		$form = new ContactForm();

		$form->setScenario(ContactForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->contactService->create($form->getDto());

		return new ContactResource($model);
	}

	/**
	 * @param $id
	 *
	 * @return ContactResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionUpdate($id): ContactResource
	{
		$model = $this->findModel($id);

		$form = new ContactForm();

		$form->setScenario(ContactForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->contactService->update($model, $form->getDto());

		return new ContactResource($model);
	}


	/**
	 * @param $id
	 *
	 * @return SuccessResponse
	 * @throws NotFoundHttpException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function actionDelete($id): SuccessResponse
	{
		$this->contactService->delete($this->findModel($id));

		return new SuccessResponse('Контакт успешно удален');
	}

	/**
	 * @return ContactCommentResource
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreateComment(): ContactCommentResource
	{
		$form = new ContactCommentForm();

		$form->setScenario(ContactCommentForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->author_id = $this->user->id;

		$form->validateOrThrow();

		$comment = $this->contactCommentService->create($form->getDto());

		return new ContactCommentResource($comment);
	}

	/**
	 * @param $id
	 *
	 * @return Contact
	 * @throws NotFoundHttpException
	 */
	protected function findModel($id): Contact
	{
		if (($model = Contact::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
