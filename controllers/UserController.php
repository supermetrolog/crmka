<?php

namespace app\controllers;

use app\dto\Auth\AuthUserAgentDto;
use app\exceptions\InvalidBearerTokenException;
use app\exceptions\InvalidPasswordException;
use app\exceptions\ValidationErrorHttpException;
use app\helpers\TokenHelper;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\LoginForm;
use app\models\forms\User\UserForm;
use app\models\forms\User\UserProfileForm;
use app\models\search\UserSearch;
use app\models\UploadFile;
use app\models\User;
use app\repositories\UserAccessTokenRepository;
use app\repositories\UserRepository;
use app\resources\Auth\AuthLoginResource;
use app\resources\User\UserAccessTokenResource;
use app\resources\User\UserResource;
use app\resources\User\UserWithContactsResource;
use app\usecases\Auth\AuthService;
use app\usecases\User\UserAccessTokenService;
use app\usecases\User\UserService;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends AppController
{
	private AuthService               $authService;
	private UserService               $userService;
	private UserAccessTokenRepository $accessTokenRepository;
	private UserRepository            $userRepository;
	private UserAccessTokenService    $accessTokenService;
	protected array                   $exceptAuthActions = ['login'];


	public function __construct(
		$id,
		$module,
		AuthService $authService,
		UserService $userService,
		UserAccessTokenRepository $accessTokenRepository,
		UserRepository $userRepository,
		UserAccessTokenService $accessTokenService,
		array $config = []
	)
	{
		$this->authService           = $authService;
		$this->userService           = $userService;
		$this->accessTokenRepository = $accessTokenRepository;
		$this->userRepository        = $userRepository;
		$this->accessTokenService    = $accessTokenService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ErrorException
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new UserSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return UserWithContactsResource::fromDataProvider($dataProvider);
	}

	/**
	 * @param $id
	 *
	 * @return array|null
	 * @throws NotFoundHttpException
	 */
	public function actionView($id): array
	{
		return UserWithContactsResource::make($this->findModel($id))->toArray();
	}

	/** Creates a new user.
	 *
	 * @return array The created user.
	 * @throws SaveModelException If the user cannot be saved.
	 * @throws Throwable If the transaction cannot be committed.
	 * @throws ValidationErrorHttpException If the user form cannot be validated.
	 * @throws Exception If the user form cannot be validated.
	 */
	public function actionCreate(): array
	{
		$form            = new UserForm();
		$userProfileForm = new UserProfileForm();

		$form->setScenario(UserForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$userProfileData = $this->request->post('userProfile');

		$userProfileForm->load($userProfileData);
		$userProfileForm->emails = ArrayHelper::getValue($userProfileData, 'emails', []);
		$userProfileForm->phones = ArrayHelper::getValue($userProfileData, 'phones', []);

		$form->validateOrThrow();
		$userProfileForm->validateOrThrow();

		$uploadFileModel        = new UploadFile();
		$uploadFileModel->files = UploadedFile::getInstancesByName('files');

		$user = $this->userService->create($form->getDto(), $userProfileForm->getDto(), $uploadFileModel);

		return UserResource::tryMakeArray($user);
	}

	/** Updates an existing user.
	 *
	 * @throws SaveModelException If the user cannot be saved.
	 * @throws Throwable If the transaction cannot be committed.
	 * @throws ValidateException If the user form cannot be validated.
	 * @throws ValidationErrorHttpException If the user form cannot be validated.
	 * @throws NotFoundHttpException If the user cannot be found.
	 */
	public function actionUpdate($id): array
	{
		$model = $this->findModel($id);

		$form            = new UserForm();
		$userProfileForm = new UserProfileForm();

		$form->setScenario(UserForm::SCENARIO_UPDATE);

		$form->load($this->request->post());

		$userProfileData = $this->request->post('userProfile');

		$userProfileForm->load($userProfileData);
		$userProfileForm->emails = ArrayHelper::getValue($userProfileData, 'emails', []);
		$userProfileForm->phones = ArrayHelper::getValue($userProfileData, 'phones', []);

		$isAdministrator = $this->user->identity->isAdministrator();

		if (isset($form->password) && !$isAdministrator) {
			throw new ForbiddenHttpException('У вас нет прав на изменение пароля сотрудника');
		}

		$form->validateOrThrow();
		$userProfileForm->validateOrThrow();

		$uploadFileModel        = new UploadFile();
		$uploadFileModel->files = UploadedFile::getInstancesByName('files');

		$user = $this->userService->update($model, $form->getDto(), $userProfileForm->getDto(), $uploadFileModel);

		return UserResource::make($user)->toArray();
	}


	/**
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$identity = $this->user->identity;

		if (!$identity->isAdministrator() && !$identity->isOwner()) {
			throw new ForbiddenHttpException('У вас нет прав на удаление пользователя');
		}

		$user = $this->findModel($id);
		$this->userService->delete($user);

		return new SuccessResponse('Пользователь успешно удален');
	}


	/**
	 * @return array
	 * @throws ErrorException
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionLogin(): array
	{
		$form = new LoginForm($this->authService);
		$form->load($this->request->post());

		try {
			$form->validateOrThrow();

			$authDto = $this->authService->login($form->getDto(), new AuthUserAgentDto([
				'agent' => $this->request->getUserAgent(),
				'ip'    => $this->request->getUserIP(),
			]));

			return AuthLoginResource::make($authDto)->toArray();
		} catch (ModelNotFoundException|InvalidPasswordException $e) {
			throw new NotFoundHttpException('Неправильный логин или пароль.');
		}
	}

	/**
	 * Logs out the current user.
	 *
	 * @return SuccessResponse The response message.
	 * @throws StaleObjectException If the user cannot be logged out
	 * @throws InvalidBearerTokenException
	 * @throws NotFoundHttpException
	 * @throws Throwable If the user cannot be logged out.
	 */
	public function actionLogout(): SuccessResponse
	{
		$token = TokenHelper::parseBearerToken($this->request->headers->get('Authorization'));
		$this->authService->logout($token);

		return new SuccessResponse('Вы вышли из аккаунта');
	}

	/**
	 * @param $id
	 *
	 * @return array
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 */
	public function actionSessions($id): array
	{
		$user     = $this->findModel($id);
		$identity = $this->user->identity;

		// TODO: Заменить на RBAC
		if ($identity->id !== $user->id && !$identity->isAdministrator() && !$identity->isOwner()) {
			throw new ForbiddenHttpException('У вас нет прав на просмотр активных сессий данного пользователя');
		}

		$sessions = $this->accessTokenRepository->findAllValidByUserId($user->id);

		return UserAccessTokenResource::collection($sessions);
	}

	/**
	 * @param $id
	 *
	 * @return SuccessResponse
	 * @throws ForbiddenHttpException
	 * @throws InvalidBearerTokenException
	 * @throws ModelNotFoundException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionDeleteSessions($id): SuccessResponse
	{
		$user     = $this->findModel($id);
		$identity = $this->user->identity;

		// TODO: Заменить на RBAC
		if ($identity->id !== $user->id && !$identity->isAdministrator() && !$identity->isOwner()) {
			throw new ForbiddenHttpException('У вас нет прав на управление сессиями данного пользователя');
		}

		if ($identity->id === $user->id) {
			$token = TokenHelper::parseBearerToken($this->request->headers->get('Authorization'));
			$this->accessTokenService->deleteByUserIdExcludingToken($user->id, $token);
		} else {
			$this->accessTokenService->deleteAllByUserId($user->id);
		}

		return new SuccessResponse('Сессии успешно удалены');
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function actionArchive($id): SuccessResponse
	{
		$identity = $this->user->identity;

		// TODO: Заменить на RBAC
		if (!$identity->isAdministrator() && !$identity->isOwner()) {
			throw new ForbiddenHttpException('У вас нет прав на архивацию пользователей');
		}

		$user = $this->findModel($id);

		$this->userService->archive($user);

		return new SuccessResponse('Пользователь отправлен в архив');
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionRestore($id): SuccessResponse
	{
		$identity = $this->user->identity;

		// TODO: Заменить на RBAC
		if (!$identity->isAdministrator() && !$identity->isOwner()) {
			throw new ForbiddenHttpException('У вас нет прав на архивацию пользователей');
		}

		$user = $this->findModel($id);

		$this->userService->restore($user);

		return new SuccessResponse('Пользователь восстановлен из архива');
	}

	/**
	 * @throws SaveModelException
	 */
	public function actionActivity(): SuccessResponse
	{
		$identity = $this->user->identity;

		$this->userService->updateActivity($identity);

		return new SuccessResponse();
	}

	public function actionOnline(): int
	{
		return $this->userRepository->getOnlineCount();
	}

	/**
	 * Finds the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id The user ID.
	 *
	 * @return User The loaded model.
	 * @throws ModelNotFoundException
	 */
	protected function findModel(int $id): User
	{
		/** @var User $user */
		$user = User::find()->with(['userProfile' => function ($query) {
			$query->with(['phones', 'emails']);
		}])->byId($id)->oneOrThrow();

		return $user;
	}
}
