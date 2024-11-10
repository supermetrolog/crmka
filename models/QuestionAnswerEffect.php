<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\EffectQuery;
use app\models\ActiveQuery\QuestionAnswerQuery;

/**
 * This is the model class for table "question_answer".
 *
 * @property int $id
 * @property int $question_answer_id
 * @property int $effect_id
 *
 */
class QuestionAnswerEffect extends AR
{
	public static function tableName(): string
	{
		return 'question_answer_effect';
	}

	public function rules(): array
	{
		return [
			[['question_answer_id', 'effect_id'], 'required'],
			[['question_answer_id', 'effect_id'], 'integer'],
			[['question_answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionAnswer::class, 'targetAttribute' => ['question_answer_id' => 'id']],
			[['effect_id'], 'exist', 'skipOnError' => true, 'targetClass' => Effect::class, 'targetAttribute' => ['effect_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'                 => 'ID',
			'question_answer_id' => 'Question Answer ID',
			'effect_id'          => 'Effect ID',
		];
	}

	public function getQuestionAnswer(): QuestionAnswerQuery
	{
		/** @var QuestionAnswerQuery */
		return $this->hasOne(QuestionAnswer::class, ['id' => 'question_answer_id']);
	}

	public function getEffect(): EffectQuery
	{
		/** @var EffectQuery */
		return $this->hasOne(Effect::class, ['id' => 'effect_id']);
	}
}
