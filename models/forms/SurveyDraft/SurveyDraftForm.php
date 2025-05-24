<?php

declare(strict_types=1);

namespace app\models\forms\SurveyDraft;

use app\dto\SurveyDraft\CreateSurveyDraftDto;
use app\dto\SurveyDraft\UpdateSurveyDraftDto;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\User;
use Exception;

class SurveyDraftForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $user_id;
	public $chat_member_id;
	public $data;

	public function rules(): array
	{
		return [
			[['user_id', 'chat_member_id'], 'required'],
			[['user_id', 'chat_member_id'], 'integer'],
			['data', 'safe'],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['chat_member_id'], 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['chat_member_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'user_id'        => 'ID сотрудника',
			'chat_member_id' => 'ID чата',
			'data'           => 'Данные'
		];
	}

	public function scenarios(): array
	{
		return [
			self::SCENARIO_CREATE => [
				'user_id',
				'chat_member_id',
				'data'
			],
			self::SCENARIO_UPDATE => [
				'data'
			],
		];
	}

	/**
	 * @return CreateSurveyDraftDto|UpdateSurveyDraftDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateSurveyDraftDto([
					'user'       => User::find()->byId((int)$this->user_id)->one(),
					'chatMember' => ChatMember::find()->byId((int)$this->chat_member_id)->one(),
					'data'       => $this->data
				]);

			default:
				return new UpdateSurveyDraftDto([
					'data' => $this->data
				]);
		}
	}
}