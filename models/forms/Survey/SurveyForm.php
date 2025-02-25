<?php

declare(strict_types=1);

namespace app\models\forms\Survey;

use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\Contact;
use app\models\Survey;
use app\models\User;
use Exception;

class SurveyForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $user_id;
	public $contact_id;
	public $chat_member_id;
	public $related_survey_id;

	public function rules(): array
	{
		return [
			[['user_id', 'contact_id', 'chat_member_id'], 'required'],
			[['user_id', 'contact_id', 'chat_member_id', 'related_survey_id'], 'integer'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
			[['chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::class, 'targetAttribute' => ['chat_member_id' => 'id']],
			[['related_survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::class, 'targetAttribute' => ['related_survey_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'user_id',
			'contact_id',
			'chat_member_id',
			'related_survey_id',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	/**
	 * @return CreateSurveyDto|UpdateSurveyDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateSurveyDto([
					'user'              => User::find()->byId($this->user_id)->one(),
					'contact'           => Contact::find()->byId($this->contact_id)->one(),
					'chatMember'        => ChatMember::find()->byId($this->chat_member_id)->one(),
					'related_survey_id' => $this->related_survey_id
				]);

			default:
				return new UpdateSurveyDto([
					'user'              => User::find()->byId($this->user_id)->one(),
					'contact'           => Contact::find()->byId($this->contact_id)->one(),
					'chatMember'        => ChatMember::find()->byId($this->chat_member_id)->one(),
					'related_survey_id' => $this->related_survey_id
				]);
		}
	}
}