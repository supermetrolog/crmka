<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\SurveyDraftQuery;
use app\models\ActiveQuery\UserQuery;
use yii\helpers\Json;

/**
 * This is the model class for table "survey".
 *
 * @property int             $id
 * @property int             $user_id
 * @property int             $chat_member_id
 * @property ?string         $data
 * @property string          $created_at
 * @property string          $updated_at
 *
 * @property-read User       $user
 * @property-read ChatMember $chatMember
 */
class SurveyDraft extends AR
{
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'survey_draft';
	}

	public static function find(): SurveyDraftQuery
	{
		return new SurveyDraftQuery(static::class);
	}

	public function rules(): array
	{
		return [
			[['user_id', 'chat_member_id'], 'required'],
			[['user_id', 'chat_member_id'], 'integer'],
			['data', 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['chat_member_id'], 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['chat_member_id' => 'id']],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getChatMember(): ChatMemberQuery
	{
		/** @var ChatMemberQuery */
		return $this->hasOne(ChatMember::class, ['id' => 'chat_member_id']);
	}

	public function getParsedData(): ?array
	{
		return Json::decode($this->data);
	}
}
