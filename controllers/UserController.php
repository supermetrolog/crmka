<?php

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use app\exceptions\ValidationErrorHttpException;
use app\helpers\TokenHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\LoginForm;
use app\models\forms\User\UserForm;
use app\models\forms\User\UserProfileForm;
use app\models\UploadFile;
use app\models\User;
use app\resources\Authentication\AuthenticationLoginResource;
use app\resources\User\UserResource;
use app\usecases\Authentication\AuthenticationService;
use app\usecases\User\UserService;
use Exception;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends ActiveController
{
	public $modelClass = 'app\models\User';

	private AuthenticationService $authService;
	private UserService           $userService;


	public function __construct(
		$id,
		$module,
		AuthenticationService $authService,
		UserService $userService,
		array $config = []
	)
	{
		$this->authService = $authService;
		$this->userService = $userService;

		parent::__construct($id, $module, $config);
	}

	public function behaviors(): array
	{
		$behaviors = parent::behaviors();

		return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['login', 'index']);
	}

	public function actions(): array
	{
		$actions = parent::actions();
		unset($actions['create']);
		unset($actions['index']);
		unset($actions['update']);
		unset($actions['delete']);
		unset($actions['view']);

		return $actions;
	}

	public function actionIndex()
	{
		return User::getUsers();
	}

	public function actionView($id)
	{
		return User::getUser($id);
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
	public function actionUpdate($id): UserResource
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

		$form->validateOrThrow();
		$userProfileForm->validateOrThrow();

		$uploadFileModel        = new UploadFile();
		$uploadFileModel->files = UploadedFile::getInstancesByName('files');

		$user = $this->userService->update($model, $form->getDto(), $userProfileForm->getDto(), $uploadFileModel);

		return new UserResource($user);
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
		$user = Yii::$app->user->identity;

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
		$form = new LoginForm();
		$form->load($this->request->post());

		if ($form->validate()) {
			$authenticated = $this->authService->authenticate($form->username, $form->password);

			return AuthenticationLoginResource::make($authenticated)->toArray();
		}

		throw new ValidationErrorHttpException($form->getErrorSummary(false));
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
		$token = TokenHelper::parseBearerToken(Yii::$app->request->headers->get('Authorization'));
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
		$user = User::findOne($id);

		if ($user instanceof User) {
			return $user;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
