<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Company;
use app\models\CompanySearch;
use app\models\forms\Company\CompanyContactsForm;
use app\models\forms\Company\CompanyForm;
use app\models\forms\Company\CompanyLogoForm;
use app\models\forms\Company\CompanyMediaForm;
use app\models\forms\Company\CompanyMiniModelsForm;
use app\models\Objects;
use app\models\views\CompanySearchView;
use app\repositories\CompanyRepository;
use app\repositories\ProductRangeRepository;
use app\resources\Company\CompanyInListResource;
use app\resources\Company\CompanyViewResource;
use app\resources\Company\CreatedCompanyResource;
use app\resources\Media\MediaShortResource;
use app\usecases\Company\CompanyService;
use app\usecases\Company\CompanyWithGeneralContactService;
use Throwable;
use yii\base\ErrorException;
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
		$model = $this->findModel($id);

		return new CompanyViewResource($model);
	}

	/**
	 * @return CompanyViewResource
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
			'productRanges' => $this->request->post('productRanges') ?? [],
			'categories'    => $this->request->post('categories') ?? [],
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
	 * @param $id
	 *
	 * @return CompanyViewResource
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionUpdate($id): CompanyViewResource
	{
		$company = $this->findModel($id);

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
			'productRanges' => $this->request->post('productRanges') ?? [],
			'categories'    => $this->request->post('categories') ?? [],
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

		return new CompanyViewResource($company);
	}

	/**
	 * @return string[]
	 * @throws ErrorException
	 */
	public function actionProductRangeList(): array
	{
		return $this->productRangeRepository->getUniqueAll();
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
		$company = $this->findModel($id);
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
		$company = $this->findModel($id);

		$form = new CompanyLogoForm();

		$form->load([
			'logo' => UploadedFile::getInstanceByName('logo'),
		]);

		$form->validateOrThrow();

		$updatedLogo = $this->companyService->updateLogo($company, $form->logo);

		return new MediaShortResource($updatedLogo);
	}

	/**
	 * @param       $id
	 *
	 * @return Company
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 */
	protected function findModel($id): Company
	{
		/** @var CompanySearchView $model */
		$model = CompanySearchView::find()
		                          ->select([
			                          Company::field('*'),
			                          'objects_count'         => 'COUNT(DISTINCT ' . Objects::field('id') . ' )',
			                          'requests_count'        => 'COUNT(DISTINCT request.id)',
			                          'contacts_count'        => 'COUNT(DISTINCT contact.id)',
			                          'active_contacts_count' => 'COUNT(DISTINCT CASE WHEN contact.status = 1 THEN contact.id ELSE NULL END)',
		                          ])
		                          ->byId($id)
		                          ->joinWith(['requests', 'contacts', 'objects'])
		                          ->with(['productRanges',
		                                  'categories',
		                                  'companyGroup',
		                                  'deals',
		                                  'files',
		                                  'dealsRequestEmpty.consultant.userProfile',
		                                  'dealsRequestEmpty.offer.generalOffersMix',
		                                  'dealsRequestEmpty.competitor',
		                                  'consultant.userProfile',
		                                  'contacts' => function ($query) {
			                                  $query->with(['phones', 'emails', 'contactComments', 'websites']);
		                                  }])
		                          ->oneOrThrow();

		return $model;
	}
}