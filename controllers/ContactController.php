<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Contact;
use app\models\ContactSearch;
use app\models\forms\Contact\ContactCommentForm;
use app\models\forms\Contact\ContactDisableForm;
use app\models\forms\Contact\ContactForm;
use app\models\forms\Contact\ContactTransferToCompanyForm;
use app\models\forms\Phone\PhoneForm;
use app\repositories\ContactRepository;
use app\resources\Contact\Comment\ContactCommentResource;
use app\resources\Contact\ContactResource;
use app\resources\Contact\ContactSearchResource;
use app\resources\Contact\ContactWithCommentsResource;
use app\resources\Phone\PhoneResource;
use app\usecases\Contact\ContactCommentService;
use app\usecases\Contact\ContactService;
use app\usecases\Phone\PhoneService;
use ErrorException;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ContactController extends AppController
{
	protected array $viewOnlyAllowedActions = ['index', 'company-contacts', 'view', 'view-phones'];

	protected array               $exceptAuthActions = ['view', 'index'];
	private ContactService        $contactService;
	private ContactCommentService $contactCommentService;
	private ContactRepository     $repository;
	private PhoneService          $phoneService;

	public function __construct(
		$id,
		$module,
		ContactService $contactService,
		ContactCommentService $contactCommentService,
		ContactRepository $repository,
		PhoneService $phoneService,
		array $config = []
	)
	{
		$this->contactService        = $contactService;
		$this->contactCommentService = $contactCommentService;
		$this->repository            = $repository;
		$this->phoneService          = $phoneService;

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

		return ContactSearchResource::fromDataProvider($dataProvider);
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

		$phoneDtos = [];

		foreach ($this->request->post('phones', []) as $phoneData) {
			$phoneForm   = $this->makePhoneForm($phoneData);
			$phoneDtos[] = $phoneForm->getDto();
		}

		$model = $this->contactService->create($form->getDto(), $phoneDtos);

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
	 * @throws ModelNotFoundException
	 * @throws ValidateException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionDisable(int $id): SuccessResponse
	{
		$contact = $this->repository->findOneOrThrow($id);

		$form = new ContactDisableForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->contactService->markAsPassive($contact, $form->getDto());

		return $this->success('Контакт переведен в пассив');
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function actionEnable(int $id): SuccessResponse
	{
		$contact = $this->repository->findOneOrThrow($id);

		$this->contactService->markAsActive($contact);

		return $this->success('Контакт успешно восстановлен из архива');
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionViewPhones(int $id): array
	{
		$contact = $this->repository->findOneOrThrow($id);

		$phones = $contact->phones;

		return PhoneResource::collection($phones);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreatePhone(int $id): PhoneResource
	{
		$contact = $this->repository->findOneOrThrow($id);

		$form = new PhoneForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$phone = $this->phoneService->createForContact($contact, $form->getDto());

		return new PhoneResource($phone);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionTransferToCompany(int $id): SuccessResponse
	{
		$contact = $this->repository->findOneOrThrow($id);

		$form = new ContactTransferToCompanyForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->contactService->transferToCompany($contact, $form->getDto());

		return $this->success('Контакт успешно переведен в компанию');
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

	/**
	 * @throws ValidateException
	 */
	protected function makePhoneForm(array $payload): PhoneForm
	{
		$form = new PhoneForm();

		$form->load($payload);

		$form->validateOrThrow();

		return $form;
	}
}
