<?php

declare(strict_types=1);

namespace app\usecases\SurveyQuestionAnswer;

use app\dto\Media\CreateMediaDto;
use app\dto\Relation\CreateRelationDto;
use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\dto\SurveyQuestionAnswer\UpdateSurveyQuestionAnswerDto;
use app\helpers\ArrayHelper;
use app\helpers\TypeConverterHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Field;
use app\models\QuestionAnswer;
use app\models\SurveyQuestionAnswer;
use app\repositories\QuestionAnswerRepository;
use app\usecases\Media\CreateMediaService;
use app\usecases\Relation\RelationService;
use Throwable;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;
use yii\db\StaleObjectException;
use yii\helpers\Json;

class SurveyQuestionAnswerService
{
	private QuestionAnswerRepository     $questionAnswerRepository;
	private CreateMediaService           $createMediaService;
	private TransactionBeginnerInterface $transactionBeginner;
	private RelationService              $relationService;

	public function __construct(QuestionAnswerRepository $questionAnswerRepository, CreateMediaService $createMediaService, TransactionBeginnerInterface $transactionBeginner, RelationService $relationService)
	{
		$this->questionAnswerRepository = $questionAnswerRepository;
		$this->createMediaService       = $createMediaService;
		$this->transactionBeginner      = $transactionBeginner;
		$this->relationService          = $relationService;
	}

	/**
	 * @throws ErrorException
	 */
	public function getBySurveyIdAndQuestionAnswerId(int $surveyId, int $questionAnswerId): ?SurveyQuestionAnswer
	{
		return SurveyQuestionAnswer::find()->bySurveyId($surveyId)->byQuestionAnswerId($questionAnswerId)->one();
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function create(CreateSurveyQuestionAnswerDto $dto, array $mediaDtos = []): SurveyQuestionAnswer
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$questionAnswer = $this->questionAnswerRepository->findOneOrThrow($dto->question_answer_id);

			$model = new SurveyQuestionAnswer([
				'question_answer_id' => $dto->question_answer_id,
				'survey_id'          => $dto->survey_id,
				'value'              => $this->encodeValue($dto->value, $questionAnswer, $mediaDtos)
			]);

			$model->saveOrThrow();

			if (ArrayHelper::notEmpty($mediaDtos) && $questionAnswer->isFilesFieldType()) {
				$this->createFiles($model, $mediaDtos);
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 */
	private function encodeValue($value, QuestionAnswer $questionAnswer, array $mediaDtos = []): ?string
	{
		$field = $questionAnswer->field;

		if ($field->field_type === Field::FIELD_TYPE_FILES) {
			return Json::encode(ArrayHelper::notEmpty($mediaDtos));
		}

		if (is_null($value)) {
			return null;
		}

		switch ($field->type) {
			case $field::TYPE_STRING:
				return Json::encode(TypeConverterHelper::toString($value));
			case $field::TYPE_INTEGER:
				return Json::encode(TypeConverterHelper::toInt($value));
			case $field::TYPE_BOOLEAN:
				return Json::encode(TypeConverterHelper::toBool($value));
			case $field::TYPE_JSON:
				return Json::encode($value);
			default:
				throw new InvalidArgumentException('Unknown field type in question answer');
		}
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws Throwable
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 */
	public function update(SurveyQuestionAnswer $model, UpdateSurveyQuestionAnswerDto $dto, array $mediaDtos = []): SurveyQuestionAnswer
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$questionAnswer = $this->questionAnswerRepository->findOneOrThrow($dto->question_answer_id);

			$model->load([
				'question_answer_id' => $dto->question_answer_id,
				'survey_id'          => $dto->survey_id,
				'value'              => $this->encodeValue($dto->value, $questionAnswer, $mediaDtos)
			]);

			$model->saveOrThrow();

			if (ArrayHelper::notEmpty($mediaDtos) && $questionAnswer->isFilesFieldType()) {
				$this->createFiles($model, $mediaDtos);
			}

			$tx->commit();

			return $model;
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(SurveyQuestionAnswer $model): void
	{
		$model->delete();
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws Throwable
	 */
	public function createFiles(SurveyQuestionAnswer $surveyQuestionAnswer, array $mediaDtos = []): void
	{
		$tx = $this->transactionBeginner->begin();

		try {
			foreach ($mediaDtos as $dto) {
				$media = $this->createMediaService->create($dto);

				$this->linkRelation($surveyQuestionAnswer, $media::getMorphClass(), $media->id);
			}

			$tx->commit();
		} catch (Throwable $th) {
			$tx->rollBack();
			throw $th;
		}
	}

	/**
	 * @param int|string $relationId
	 *
	 * @throws SaveModelException
	 */
	private function linkRelation(SurveyQuestionAnswer $surveyQuestionAnswer, string $relationType, $relationId): void
	{
		$this->relationService->create(
			new CreateRelationDto([
				'first_type'  => $surveyQuestionAnswer::getMorphClass(),
				'first_id'    => $surveyQuestionAnswer->id,
				'second_type' => $relationType,
				'second_id'   => $relationId,
			])
		);
	}
}