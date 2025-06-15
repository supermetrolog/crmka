<?php

declare(strict_types=1);

namespace app\models\forms\Survey;

use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\kernel\common\models\Form\Form;
use app\models\Call;
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
	public $type;
	public $comment;
	public $related_survey_id;
	public $call_ids = [];

	public function rules(): array
	{
		return [
			[['user_id', 'chat_member_id', 'type'], 'required'],
			[['user_id', 'contact_id', 'chat_member_id', 'related_survey_id'], 'integer'],
			[['type'], 'string', 'max' => 16],
			[['comment'], 'string', 'max' => 1024],
			[['type'], 'in', 'range' => Survey::getTypes()],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['contact_id'], 'exist', 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
			[['chat_member_id'], 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['chat_member_id' => 'id']],
			[['related_survey_id'], 'exist', 'targetClass' => Survey::class, 'targetAttribute' => ['related_survey_id' => 'id']],
			['call_ids', 'each', 'rule' => ['exist', 'targetClass' => Call::class, 'targetAttribute' => ['call_ids' => 'id']]],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'user_id'           => 'ID пользователя',
			'contact_id'        => 'ID контакта',
			'chat_member_id'    => 'ID чата',
			'related_survey_id' => 'ID связанного опроса',
			'call_ids'          => 'ID звонков',
			'type'              => 'Тип опроса',
			'comment'           => 'Комментарий',
		];
	}

	public function scenarios(): array
	{
		return [
			self::SCENARIO_CREATE => ['call_ids', 'type', 'related_survey_id', 'chat_member_id', 'user_id', 'contact_id', 'comment'],
			self::SCENARIO_UPDATE => ['contact_id', 'call_ids', 'comment'],
		];
	}

	private function getContact(): ?Contact
	{
		return $this->contact_id ? Contact::find()->byId((int)$this->contact_id)->one() : null;
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
					'user'              => User::find()->byId((int)$this->user_id)->one(),
					'contact'           => $this->getContact(),
					'chatMember'        => ChatMember::find()->byId((int)$this->chat_member_id)->one(),
					'related_survey_id' => $this->related_survey_id,
					'calls'             => Call::find()->byIds($this->call_ids)->all(),
					'type'              => $this->type,
					'comment'           => $this->comment
				]);

			default:
				return new UpdateSurveyDto([
					'contact' => $this->getContact(),
					'calls'   => Call::find()->byIds($this->call_ids)->all(),
					'comment' => $this->comment
				]);
		}
	}
}