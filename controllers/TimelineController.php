<?php

namespace app\controllers;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Timeline\TimelineCommentForm;
use app\models\forms\Timeline\TimelineStepCommentForm;
use app\models\forms\Timeline\TimelineStepFeedbackForm;
use app\models\forms\Timeline\TimelineStepForm;
use app\models\forms\Timeline\TimelineStepObjectForm;
use app\models\forms\Timeline\TimelineViewForm;
use app\repositories\TimelineCommentRepository;
use app\repositories\TimelineRepository;
use app\repositories\TimelineStepRepository;
use app\resources\Timeline\TimelineCommentResource;
use app\resources\Timeline\TimelineFullResource;
use app\resources\Timeline\TimelineViewResource;
use app\usecases\Timeline\TimelineService;
use ErrorException;
use Throwable;
use yii\web\ForbiddenHttpException;

class TimelineController extends AppController
{
	private TimelineRepository        $timelineRepository;
	private TimelineService           $timelineService;
	private TimelineCommentRepository $timelineCommentRepository;
	private TimelineStepRepository    $timelineStepRepository;

	public function __construct(
		$id,
		$module,
		TimelineService $timelineService,
		TimelineRepository $timelineRepository,
		TimelineCommentRepository $timelineCommentRepository,
		TimelineStepRepository $timelineStepRepository,
		array $config = []
	)
	{
		$this->timelineService           = $timelineService;
		$this->timelineRepository        = $timelineRepository;
		$this->timelineCommentRepository = $timelineCommentRepository;
		$this->timelineStepRepository    = $timelineStepRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): TimelineViewResource
	{
		$form = new TimelineViewForm();

		$form->load($this->request->get());

		$form->validateOrThrow();

		$timeline         = $this->timelineRepository->findOneByRequestIdAndConsultantIdWithRelations($form->request_id, $form->consultant_id);
		$requestTimelines = $this->timelineRepository->findAllByRequestId($form->request_id);

		return new TimelineViewResource($timeline, $requestTimelines);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionView($id): TimelineFullResource
	{
		$timeline = $this->timelineRepository->findOneByIdWithRelationsOrThrow((int)$id);

		return new TimelineFullResource($timeline);
	}

	public function actionSearch(): void
	{
		// TODO: Сделать поиск по таймлайнам с фильтрами + query
	}

	/**
	 * @throws ForbiddenHttpException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionUpdateStep($id): SuccessResponse
	{
		$step = $this->timelineStepRepository->findOneOrThrow((int)$id);

		if ($step->timeline->consultant_id !== $this->user->id) {
			throw new ForbiddenHttpException('У вас нет прав на обновление даннного таймлайна.');
		}

		$stepForm = new TimelineStepForm();
		$stepForm->load($this->request->post());
		$stepForm->validateOrThrow();

		$commentDtos = [];

		foreach ($this->request->post('comments', []) as $comment) {
			$commentForm   = $this->makeStepCommentForm($comment);
			$commentDtos[] = $commentForm->getDto();
		}

		$objectDtos = [];

		foreach ($this->request->post('objects', []) as $object) {
			$objectForm   = $this->makeStepObjectForm($object);
			$objectDtos[] = $objectForm->getDto();
		}

		$feedbackDtos = [];

		foreach ($this->request->post('feedback_ways', []) as $way) {
			$feedbackForm   = $this->makeStepFeedbackForm($way);
			$feedbackDtos[] = $feedbackForm->getDto();
		}

		$this->timelineService->updateStep($step, $stepForm->getDto(), $commentDtos, $objectDtos, $feedbackDtos);

		return $this->success('Шаг успешно обновлен.');
	}

	/**
	 * @return TimelineCommentResource[]|ErrorResponse
	 * @throws ErrorException
	 */
	public function actionActionComments($id)
	{
		try {
			$timeline = $this->timelineRepository->findOneOrThrow($id);

			$comments = $this->timelineCommentRepository->findAllByTimelineId($timeline->id);

			return TimelineCommentResource::collection($comments);
		} catch (ModelNotFoundException $exception) {
			return $this->error('Таймлайна с таким ID не существует', 404);
		}
	}

	/**
	 * @throws ValidateException
	 * @throws Throwable
	 * @throws ValidationErrorHttpException
	 */
	public function actionAddActionComment(): TimelineCommentResource
	{
		$form = new TimelineCommentForm();

		$form->load($this->request->post());
		$form->validateOrThrow();

		$comment = $this->timelineService->createComment($form->getDto());

		return new TimelineCommentResource($comment);
	}

	/**
	 * @throws ValidateException
	 */
	private function makeStepCommentForm(array $payload): TimelineStepCommentForm
	{
		$form = new TimelineStepCommentForm();
		$form->load($payload);
		$form->validateOrThrow();

		return $form;
	}

	/**
	 * @throws ValidateException
	 */
	private function makeStepObjectForm(array $payload): TimelineStepObjectForm
	{
		$form = new TimelineStepObjectForm();
		$form->load($payload);
		$form->validateOrThrow();

		return $form;
	}

	/**
	 * @throws ValidateException
	 */
	private function makeStepFeedbackForm(array $payload): TimelineStepFeedbackForm
	{
		$form = new TimelineStepFeedbackForm();
		$form->load($payload);
		$form->validateOrThrow();

		return $form;
	}
}
