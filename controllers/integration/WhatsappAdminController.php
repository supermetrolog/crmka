<?php
declare(strict_types=1);

namespace app\controllers\integration;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\search\UserWhatsappLinkSearch;
use app\repositories\UserRepository;
use app\repositories\UserWhatsappLinkRepository;
use app\resources\Whatsapp\UserWhatsappLinkResource;
use app\usecases\Whatsapp\WhatsappLinkService;
use ErrorException;
use yii\data\ActiveDataProvider;

final class WhatsappAdminController extends AppController
{
	protected WhatsappLinkService        $linkService;
	protected UserRepository             $userRepository;
	protected UserWhatsappLinkRepository $linkRepository;

	public function __construct(
		$id,
		$module,
		WhatsappLinkService $linkService,
		UserRepository $userRepository,
		UserWhatsappLinkRepository $linkRepository,
		$config = []
	)
	{
		$this->linkService    = $linkService;
		$this->userRepository = $userRepository;
		$this->linkRepository = $linkRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionList(): ActiveDataProvider
	{
		$searcher = new UserWhatsappLinkSearch();

		return UserWhatsappLinkResource::fromDataProvider($searcher->search($this->request->get()));
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionRevokeUser(int $id): SuccessResponse
	{
		$user = $this->userRepository->findOneOrThrow($id);

		$this->linkService->revokeByUser($user);

		return $this->success('Whatsapp аккаунт отвязан');
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function actionRevokeLink(int $id): SuccessResponse
	{
		$link = $this->linkRepository->findOneOrThrow($id);

		$this->linkService->revoke($link);

		return $this->success('Whatsapp аккаунт отвязан');
	}
}
