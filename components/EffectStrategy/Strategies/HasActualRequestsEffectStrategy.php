<?php

namespace app\components\EffectStrategy\Strategies;

use app\components\EffectStrategy\AbstractEffectStrategy;
use app\dto\Request\PassiveRequestDto;
use app\helpers\ArrayHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\ChatMemberMessage;
use app\models\QuestionAnswer;
use app\models\Request;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\repositories\RequestRepository;
use app\usecases\Request\RequestService;
use Throwable;
use yii\base\Exception;

class HasActualRequestsEffectStrategy extends AbstractEffectStrategy
{
	private TransactionBeginnerInterface $transactionBeginner;
	private RequestRepository            $requestRepository;
	private RequestService               $requestService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		RequestRepository $requestRepository,
		RequestService $requestService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->requestRepository   = $requestRepository;
		$this->requestService      = $requestService;
	}

	/**
	 * @throws Exception
	 */
	public function shouldBeProcessed(Survey $survey, QuestionAnswer $answer): bool
	{
		return $answer->surveyQuestionAnswer->hasAnswer() && ArrayHelper::notEmpty($answer->surveyQuestionAnswer->getJSON());
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function process(Survey $survey, SurveyQuestionAnswer $surveyQuestionAnswer, ChatMemberMessage $surveyChatMemberMessage): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$requests = $surveyQuestionAnswer->getJSON();

			foreach ($requests as $request) {
				$requestMustBePassive = !TypeConverterHelper::toBool(ArrayHelper::getValue($request, 'answer', true));

				if ($requestMustBePassive) {
					$requestId = TypeConverterHelper::toInt(ArrayHelper::getValue($request, 'id'));

					$request = $this->requestRepository->findOneOrThrow($requestId);

					$this->setRequestAsPassive($request);
				}
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	private function setRequestAsPassive(Request $request): void
	{
		$this->requestService->markAsPassive(
			$request,
			new PassiveRequestDto([
				'passive_why' => Request::PASSIVE_WHY_SURVEY
			])
		);
	}
}