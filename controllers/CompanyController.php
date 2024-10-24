<?php

namespace app\controllers;

use app\dto\Company\CompanyContactsDto;
use app\dto\Company\CompanyMiniModelsDto;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\Company;
use app\models\CompanySearch;
use app\models\forms\Company\CompanyForm;
use app\models\forms\Company\CompanyMediaForm;
use app\repositories\CompanyRepository;
use app\repositories\ProductRangeRepository;
use app\resources\Company\CompanyInListResource;
use app\resources\Company\CompanyViewResource;
use app\usecases\Company\CompanyService;
use app\usecases\Company\CompanyWithGeneralContactService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class CompanyController extends AppController
{
	private CompanyWithGeneralContactService $companyWithGeneralContactService;

	private ProductrangeRepository $productRangeRepository;

	private CompanyRepository $companyRepository;

	private CompanyService $companyService;

	public function __construct(
		$id,
		$module,
		CompanyWithGeneralContactService $companyWithGeneralContactService,
		ProductrangeRepository $productRangeRepository,
		CompanyRepository $companyRepository,
		CompanyService $companyService,
		array $config = []
	)
	{
		$this->companyWithGeneralContactService = $companyWithGeneralContactService;
		$this->companyService                   = $companyService;
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
	public function actionCreate(): CompanyViewResource
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

		$companyMiniModelsDto = new CompanyMiniModelsDto([
			'productRanges' => $this->request->post('productRanges') ?? [],
			'categories'    => $this->request->post('categories') ?? []
		]);

		$contactsData = $this->request->post('contacts');

		if ($contactsData) {
			$companyContactsDto = new CompanyContactsDto($contactsData);

			$company = $this->companyWithGeneralContactService->create(
				$form->getDto(),
				$companyMiniModelsDto,
				$companyContactsDto,
				$companyMediaForm->getDto()
			);
		} else {
			$company = $this->companyService->create(
				$form->getDto(),
				$companyMiniModelsDto,
				$companyMediaForm->getDto()
			);
		}

		return new CompanyViewResource($company);
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

		$companyMiniModelsDto = new CompanyMiniModelsDto([
			'productRanges' => $this->request->post('productRanges') ?? [],
			'categories'    => $this->request->post('categories') ?? []
		]);

		$contactsData = $this->request->post('contacts');

		$companyContactsDto = new CompanyContactsDto([
			'emails'   => $contactsData['emails'] ?? [],
			'phones'   => $contactsData['phones'] ?? [],
			'websites' => $contactsData['websites'] ?? []
		]);

		$company = $this->companyWithGeneralContactService->update(
			$company,
			$form->getDto(),
			$companyMiniModelsDto,
			$companyContactsDto,
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
	 * @param       $id
	 *
	 * @return Company
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 */
	protected function findModel($id): Company
	{
		/** @var Company $model */
		$model = Company::find()
		                ->select(Company::field('*'))
		                ->byId($id)
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