<?php

namespace app\models;

use app\enum\Survey\SurveyActionStatusEnum;
use app\enum\Survey\SurveyActionTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User\User;

/**
 * @property int         $id
 * @property int         $survey_id
 * @property ?int        $target_id
 * @property int         $created_by_id
 * @property string      $status
 * @property string      $type
 * @property ?string     $comment
 * @property string      $created_at
 * @property string      $updated_at
 * @property string      $deleted_at
 * @property string      $completed_at
 *
 * @property-read Survey $survey
 * @property-read User   $createdBy
 */
class SurveyAction extends AR
{
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'survey_action';
	}

	public function rules(): array
	{
		return [
			[['survey_id', 'type', 'status', 'created_by_id'], 'required'],
			[['survey_id', 'target_id', 'created_by_id'], 'integer'],
			['status', EnumValidator::class, 'enumClass' => SurveyActionStatusEnum::class],
			['type', EnumValidator::class, 'enumClass' => SurveyActionTypeEnum::class],
			[['comment'], 'string', 'max' => 1024],
			[['created_at', 'updated_at', 'completed_at', 'deleted_at'], 'safe'],
			['survey_id', 'exist', 'targetClass' => Survey::class, 'targetAttribute' => 'id'],
			['created_by_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public function getSurvey(): SurveyQuery
	{
		/** @var SurveyQuery */
		return $this->hasOne(Survey::class, ['id' => 'survey_id']);
	}

	public function getCreatedBy(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'created_by_id']);
	}

	public static function find(): AQ
	{
		return (new AQ(static::class))->andWhereNull('deleted_at');
	}
}
