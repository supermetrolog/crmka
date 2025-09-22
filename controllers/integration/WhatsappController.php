<?php
declare(strict_types=1);

namespace app\controllers\integration;

use app\exceptions\services\Whatsapp\WhatsappPhoneNotExistsException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Integration\WhatsappLinkForm;
use app\resources\Whatsapp\StatusWhatsappLinkResource;
use app\resources\Whatsapp\UserWhatsappLinkResource;
use app\usecases\Whatsapp\WhatsappLinkService;

final class WhatsappController extends AppController
{
	protected WhatsappLinkService $linkService;

	public function __construct(
		$id,
		$module,
		WhatsappLinkService $linkService,
		$config = []
	)
	{
		$this->linkService = $linkService;

		parent::__construct($id, $module, $config);
	}


	/**
	 * @throws ValidateException
	 */
	public function actionLink()
	{
		$form = new WhatsappLinkForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		try {
			$link = $this->linkService->link($this->user->identity, $form->phone);

			return new UserWhatsappLinkResource($link);
		} catch (WhatsappPhoneNotExistsException $th) {
			return $this->error('Номер Whatsapp не найден.');
		}
	}

	public function actionStatus(): StatusWhatsappLinkResource
	{
		$dto = $this->linkService->getStatusForUser($this->user->identity);

		return new StatusWhatsappLinkResource($dto);
	}

	public function actionRevoke(): SuccessResponse
	{
		try {
			$this->linkService->revokeByUser($this->user->identity);

			return $this->success('Whatsapp аккаунт отвязан');
		} catch (ModelNotFoundException $th) {
			return $this->success('К вашему аккаунту не привязан Whatsapp аккаунт');
		}
	}
}
