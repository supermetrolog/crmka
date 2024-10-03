<?php

namespace app\controllers;

use app\dto\Auth\AuthUserAgentDto;
use app\exceptions\ValidationErrorHttpException;
use app\helpers\TokenHelper;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\LoginForm;
use app\models\forms\User\UserForm;
use app\models\forms\User\UserProfileForm;
use app\models\search\UserSearch;
use app\models\UploadFile;
use app\models\User;
use app\resources\Auth\AuthLoginResource;
use app\resources\User\UserResource;
use app\resources\User\UserWithContactsResource;
use app\usecases\Auth\AuthService;
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
	private AuthService $authService;
	private UserService $userService;
	protected array     $exceptAuthActions = ['login'];


	public function __construct(
		$id,
		$module,
		AuthService $authService,
		UserService $userService,
		array $config = []
	)
	{
		$this->authService = $authService;
		$this->userService = $userService;

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


	/** Deletes an existing user.
	 *
	 * @param int $id The user ID.
	 *
	 * @return SuccessResponse The response message.
	 * @throws ForbiddenHttpException If the user is not an administrator.
	 * @throws NotFoundHttpException If the user cannot be found.
	 * @throws SaveModelException If the user cannot be saved.
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$user = $this->user->identity;

		if ($user === null || !$user->isAdministrator()) {
			throw new ForbiddenHttpException('У вас нет прав на удаление пользователя');
		}

		$model = $this->findModel($id);
		$this->userService->delete($model);

		return new SuccessResponse('Пользователь успешно удален');
	}


	/**
	 * Logs in a user.
	 *
	 * @return array The authenticated user with the access token.
	 * @throws ValidationErrorHttpException If the login form cannot be validated.
	 * @throws ErrorException If the access token cannot be generated.
	 * @throws Exception If the access token cannot be saved.
	 */
	public function actionLogin(): array
	{
		$form = new LoginForm($this->authService);
		$form->load($this->request->post());

		$form->validateOrThrow();

		$authDto = $this->authService->login($form->getDto(), new AuthUserAgentDto([
			'agent' => $this->request->getUserAgent(),
			'ip'    => $this->request->getUserIP(),
		]));

		return AuthLoginResource::make($authDto)->toArray();
	}

	/**
	 * Logs out the current user.
	 *
	 * @return SuccessResponse The response message.
	 * @throws StaleObjectException If the user cannot be logged out.
	 * @throws Throwable If the user cannot be logged out.
	 */
	public function actionLogout(): SuccessResponse
	{
		$token = TokenHelper::parseBearerToken($this->request->headers->get('Authorization'));
		$this->authService->logout($token);

		return new SuccessResponse('Вы вышли из аккаунт');
	}

	/**
	 * Finds the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id The user ID.
	 *
	 * @return User The loaded model.
	 * @throws NotFoundHttpException If the model cannot be found.
	 */
	protected function findModel(int $id): User
	{
		$user = User::find()->with(['userProfile' => function ($query) {
			$query->with(['phones', 'emails']);
		}])->byId($id)->one();

		if ($user instanceof User) {
			return $user;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
