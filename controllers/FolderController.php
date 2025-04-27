<?php

namespace app\controllers;

use app\dto\Folder\EntityInFolderDto;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Folder\FolderEntityForm;
use app\models\forms\Folder\FolderForm;
use app\models\forms\Folder\FoldersReorderForm;
use app\models\search\FolderEntitySearch;
use app\models\search\FolderSearch;
use app\repositories\FolderRepository;
use app\resources\Folder\EntityInFolderResource;
use app\resources\Folder\FolderResource;
use app\resources\Folder\FolderSearchResource;
use app\usecases\Folder\FolderService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\ForbiddenHttpException;

class FolderController extends AppController
{
	private FolderService    $folderService;
	private FolderRepository $folderRepository;

	public function __construct(
		$id,
		$module,
		FolderService $folderService,
		FolderRepository $folderRepository,
		array $config = []
	)
	{
		$this->folderService    = $folderService;
		$this->folderRepository = $folderRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new FolderSearch();

		$searchModel->user_id = $this->user->id;

		$dataProvider = $searchModel->search($this->request->get());

		return FolderSearchResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionCreate(): FolderResource
	{
		$form = new FolderForm();

		$form->setScenario(FolderForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->user_id = $this->user->id;

		$form->validateOrThrow();

		$model = $this->folderService->create($form->getDto());

		return new FolderResource($model);
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 */
	public function actionUpdate(int $id): FolderResource
	{
		$folder = $this->folderRepository->findOneOrThrow($id);

		$identity = $this->user->identity;

		if ($folder->user_id !== $identity->id) {
			throw new ForbiddenHttpException('У вас нет прав на редактирование этой папки.');
		}

		$form = new FolderForm();

		$form->setScenario(FolderForm::SCENARIO_UPDATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->folderService->update($folder, $form->getDto());

		return new FolderResource($model);
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$folder = $this->folderRepository->findOneOrThrow($id);

		$identity = $this->user->identity;

		if ($folder->user_id !== $identity->id) {
			throw new ForbiddenHttpException('У вас нет прав на редактирование этой папки.');
		}

		$this->folderService->delete($folder);

		return $this->success('Папка успешно удалена');
	}

	/**
	 * @throws Throwable
	 */
	public function actionReorder(): SuccessResponse
	{
		$form = new FoldersReorderForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->folderService->reorderFolders($form->getDtos());

		return $this->success();
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionEntities(): ActiveDataProvider
	{

		$searchModel = new FolderEntitySearch();

		$searchModel->user_id = $this->user->id;

		$dataProvider = $searchModel->search($this->request->get());

		return EntityInFolderResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionAddEntities(int $id): SuccessResponse
	{
		$folder = $this->folderRepository->findOneOrThrow($id);

		$identity = $this->user->identity;

		if ($folder->user_id !== $identity->id) {
			throw new ForbiddenHttpException('У вас нет прав на управление этой папкой.');
		}

		$this->folderService->addEntitiesToFolder($folder, $this->collectFolderEntityDtos());

		return $this->success();
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionRemoveEntities(int $id): SuccessResponse
	{
		$folder = $this->folderRepository->findOneOrThrow($id);

		$identity = $this->user->identity;

		if ($folder->user_id !== $identity->id) {
			throw new ForbiddenHttpException('У вас нет прав на управление этой папкой.');
		}

		$this->folderService->removeEntitiesFromFolder($folder, $this->collectFolderEntityDtos());

		return $this->success();
	}

	/**
	 * @return EntityInFolderDto[]
	 * @throws ValidateException
	 */
	private function collectFolderEntityDtos(string $key = 'entities'): array
	{
		$dtos = [];

		foreach ($this->request->post($key, []) as $entityPayload) {
			$form = $this->makeFolderEntityForm($entityPayload);

			$dtos[] = $form->getDto();
		}

		return $dtos;
	}

	/**
	 * @throws ValidateException
	 */
	private function makeFolderEntityForm(array $payload): FolderEntityForm
	{
		$form = new FolderEntityForm();

		$form->load($payload);

		$form->validateOrThrow();

		return $form;
	}
}
