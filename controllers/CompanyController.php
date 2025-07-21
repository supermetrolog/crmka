<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\CompanySearch;
use app\models\forms\ChatMember\ChatMemberMessageForm;
use app\models\forms\Company\CompanyChangeConsultantForm;
use app\models\forms\Company\CompanyContactsForm;
use app\models\forms\Company\CompanyDisableForm;
use app\models\forms\Company\CompanyForm;
use app\models\forms\Company\CompanyLogoForm;
use app\models\forms\Company\CompanyMediaForm;
use app\models\forms\Company\CompanyMiniModelsForm;
use app\models\forms\Company\CompanyPinMessageForm;
use app\repositories\CompanyRepository;
use app\repositories\ProductRangeRepository;
use app\resources\Company\CompanyInListResource;
use app\resources\Company\CompanyViewResource;
use app\resources\Company\CreatedCompanyResource;
use app\resources\EntityPinnedMessage\EntityPinnedMessageResource;
use app\resources\Media\MediaShortResource;
use app\resources\ProductRange\ProductRangeResource;
use app\usecases\Company\CompanyService;
use app\usecases\Company\CompanyWithGeneralContactService;
use Throwable;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\UploadedFile;

class CompanyController extends AppController
{
	protected array                          $exceptAuthActions = ['index'];
	private CompanyWithGeneralContactService $companyWithGeneralContactService;

	private ProductRangeRepository $productRangeRepository;

	private CompanyRepository $companyRepository;

	private CompanyService $companyService;

	public function __construct(
		$id,
		$module,
		CompanyService $companyService,
		CompanyWithGeneralContactService $companyWithGeneralContactService,
		ProductRangeRepository $productRangeRepository,
		CompanyRepository $companyRepository,
		array $config = []
	)
	{
		$this->companyService                   = $companyService;
		$this->companyWithGeneralContactService = $companyWithGeneralContactService;
		$this->productRangeRepository           = $productRangeRepository;
		$this->companyRepository                = $companyRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ErrorException
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new CompanySearch();

		$dataProvider = $searchModel->search($this->request->get());

		return CompanyInListResource::fromDataProvider($dataProvider);
	}

	/**
	 * @param $id
	 *
	 * @return CompanyViewResource
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 */
	public function actionView($id): CompanyViewResource
	{
		$model = $this->companyRepository->findOneOrThrowWithRelations($id);

		return new CompanyViewResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionCreate(): CreatedCompanyResource
	{
		$form = new CompanyForm();

		$form->load($this->request->post());
		$form->validateOrThrow();

		$companyMediaForm = new CompanyMediaForm();
		$companyMediaForm->load([
			'logo'  => UploadedFile::getInstanceByName('logo'),
			'files' => UploadedFile::getInstancesByName('files'),
		]);
		$companyMediaForm->validateOrThrow();

		$companyMiniModelsForm = new CompanyMiniModelsForm();
		$companyMiniModelsForm->load([
			'productRanges' => $this->request->post('productRanges', []),
			'categories'    => $this->request->post('categories', []),
		]);
		$companyMiniModelsForm->validateOrThrow();

		$contactsData        = $this->request->post('contacts');
		$companyContactsForm = new CompanyContactsForm();
		$companyContactsForm->load([
			'emails'   => $contactsData['emails'] ?? [],
			'phones'   => $contactsData['phones'] ?? [],
			'websites' => $contactsData['websites'] ?? []
		]);
		$companyContactsForm->validateOrThrow();

		$company = $this->companyWithGeneralContactService->create(
			$form->getDto(),
			$companyMiniModelsForm->getDto(),
			$companyContactsForm->getDto(),
			$companyMediaForm->getDto()
		);


		return new CreatedCompanyResource($company);
	}

	/**
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionUpdate($id): CreatedCompanyResource
	{
		$company = $this->companyRepository->findOneOrThrow($id);

		$form = new CompanyForm();

		$form->load($this->request->post());
		$form->validateOrThrow();

		$companyMediaForm = new CompanyMediaForm();
		$companyMediaForm->load([
			'logo'  => UploadedFile::getInstanceByName('new_logo'),
			'files' => UploadedFile::getInstancesByName('new_files'),
		]);
		$companyMediaForm->validateOrThrow();

		$companyMiniModelsForm = new CompanyMiniModelsForm();
		$companyMiniModelsForm->load([
			'productRanges' => $this->request->post('productRanges', []),
			'categories'    => $this->request->post('categories', []),
		]);
		$companyMiniModelsForm->validateOrThrow();

		$contactsData        = $this->request->post('contacts');
		$companyContactsForm = new CompanyContactsForm();
		$companyContactsForm->load([
			'emails'   => $contactsData['emails'] ?? [],
			'phones'   => $contactsData['phones'] ?? [],
			'websites' => $contactsData['websites'] ?? []
		]);
		$companyContactsForm->validateOrThrow();

		$company = $this->companyWithGeneralContactService->update(
			$company,
			$form->getDto(),
			$companyMiniModelsForm->getDto(),
			$companyContactsForm->getDto(),
			$companyMediaForm->getDto()
		);

		return new CreatedCompanyResource($company);
	}

	/**
	 * @return string[]
	 * @throws ErrorException
	 */
	public function actionProductRangeList(): array
	{
		$resources = $this->productRangeRepository->getUniqueAll();

		return ProductRangeResource::collection($resources);
	}

	/**
	 * @return string[]
	 * @throws ErrorException
	 */
	public function actionInTheBankList(): array
	{
		return $this->companyRepository->getBankNameUniqueAll();
	}

	/**
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function actionDeleteLogo($id): SuccessResponse
	{
		$company = $this->companyRepository->findOneOrThrow($id);
		$this->companyService->deleteLogo($company);

		return new SuccessResponse('Логотип компании удален');
	}

	/**
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionUpdateLogo($id): MediaShortResource
	{
		$company = $this->companyRepository->findOneOrThrow($id);

		$form = new CompanyLogoForm();

		$form->load([
			'logo' => UploadedFile::getInstanceByName('logo'),
		]);

		$form->validateOrThrow();

		$updatedLogo = $this->companyService->updateLogo($company, $form->logo);

		return new MediaShortResource($updatedLogo);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws ValidateException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionDisable(int $id): SuccessResponse
	{
		$company = $this->companyRepository->findOneOrThrow($id);

		$form = new CompanyDisableForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->companyService->markAsPassive($company, $form->getDto(), $this->user->identity);

		return $this->success('Компания переведена в пассив');
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function actionEnable(int $id): SuccessResponse
	{
		$company = $this->companyRepository->findOneOrThrow($id);

		$this->companyService->markAsActive($company, $this->user->identity);

		return $this->success('Компания успешно восстановлена из архива');
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function actionPinMessage(int $id): EntityPinnedMessageResource
	{
		$company = $this->companyRepository->findOneOrThrow($id);

		$form = new CompanyPinMessageForm();

		$form->load($this->request->post());

		$form->user = $this->user->identity;

		$form->validateOrThrow();

		$message = $this->companyService->pinMessage($company, $form->getDto());

		return new EntityPinnedMessageResource($message);
	}

	/**
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function actionCreatePinnedMessage(int $id): EntityPinnedMessageResource
	{
		$company = $this->companyRepository->findOneOrThrow($id);

		$form = new ChatMemberMessageForm();
		$form->setScenario(ChatMemberMessageForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->from_chat_member_id = $this->user->identity->chatMember->id;
		$form->to_chat_member_id   = $company->chatMember->id;

		$form->validateOrThrow();

		$message = $this->companyService->createPinnedMessage($company, $form->getDto());

		return new EntityPinnedMessageResource($message);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionChangeConsultant($id)
	{
		$company = $this->companyRepository->findOneOrThrow($id);

		$form = new CompanyChangeConsultantForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$model = $this->companyService->changeConsultant($company, $form->getDto());

			return new CreatedCompanyResource($model);
		} catch (InvalidArgumentException $e) {
			return $this->error('Консультант уже назначен на эту компанию');
		}
	}
}