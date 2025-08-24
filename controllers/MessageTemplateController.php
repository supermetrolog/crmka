<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\MessageTemplate\Factories\TemplateFactory;
use app\components\MessageTemplate\Forms\ChannelForm;
use app\components\MessageTemplate\Resources\MessageTemplateResource;
use app\components\MessageTemplate\Services\MessageTemplateService;
use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ValidateException;
use InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class MessageTemplateController extends AppController
{
	private MessageTemplateService $service;
	private TemplateFactory        $messageTemplateFactory;

	public function __construct($id, $module, MessageTemplateService $service, TemplateFactory $messageTemplateFactory, $config = [])
	{
		parent::__construct($id, $module, $config);

		$this->service                = $service;
		$this->messageTemplateFactory = $messageTemplateFactory;
	}

	/**
	 * @throws ValidateException
	 * @throws ValidationErrorHttpException
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function actionRender(string $template): MessageTemplateResource
	{
		$form = new ChannelForm();

		$form->load($this->request->get());
		$form->validateOrThrow();

		try {
			$messageTemplate = $this->messageTemplateFactory->create($template);
		} catch (InvalidArgumentException $th) {
			throw new ValidationErrorHttpException('Шаблон не найден');
		}

		$templateForm = $messageTemplate->createForm();

		$templateForm->load($this->request->get());
		$templateForm->validateOrThrow();

		$dto = $templateForm->getDto();

		$dto->user = $this->user->identity;

		$renderedMessage = $this->service->render($messageTemplate, $dto, $form->channel);

		return new MessageTemplateResource($template, $form->channel, $renderedMessage);
	}
}
