<?php

namespace app\controllers;

use app\dto\Letter\LetterContactEventDto;
use app\enum\Letter\LetterContactEventTypeEnum;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\repositories\LetterContactRepository;
use app\usecases\Letter\LetterContactEventService;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Response;

class LetterTrackingController extends AppController
{
	protected array $exceptAuthActions              = ['open'];
	protected array $exceptContentNegotiatorActions = ['open'];

	private LetterContactEventService $letterContactEventService;
	private LetterContactRepository   $letterContactRepository;

	public function __construct(
		$id,
		$module,
		LetterContactEventService $letterContactEventService,
		LetterContactRepository $letterContactRepository,
		array $config = []
	)
	{
		$this->letterContactEventService = $letterContactEventService;
		$this->letterContactRepository   = $letterContactRepository;

		parent::__construct($id, $module, $config);
	}

	public function actionIndex(): void
	{
		// todo: list of events
	}

	/**
	 * @throws SaveModelException
	 * @throws RangeNotSatisfiableHttpException
	 */
	public function actionOpen(int $letter_contact_id): Response
	{
		$letterContact = $this->letterContactRepository->findOne($letter_contact_id);

		if ($letterContact) {
			$this->letterContactEventService->create(
				new LetterContactEventDto([
					'letterContact' => $letterContact,
					'eventType'     => LetterContactEventTypeEnum::OPEN,
					'userAgent'     => $this->request->getUserAgent(),
					'ip'            => $this->request->getUserIP()
				])
			);
		}

		$response = $this->response;

		$response->getHeaders()->set('Content-Type', 'image/png');

		return $response->sendContentAsFile(
			base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/wIAAgMBApTjT9sAAAAASUVORK5CYII='),
			'raysarma.png',
			['mimeType' => 'image/png']
		);
	}
}
