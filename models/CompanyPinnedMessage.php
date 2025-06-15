<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\CompanyPinnedMessageQuery;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\UserQuery;

/**
 * @property int                    $id
 * @property int                    $company_id
 * @property int                    $created_by_id
 * @property int                    $chat_member_message_id
 * @property int                    $created_at
 * @property int                    $updated_at
 * @property int                    $deleted_at
 *
 * @property-read ChatMemberMessage $chatMemberMessage
 * @property-read Company           $company
 * @property-read User              $createdBy
 */
class CompanyPinnedMessage extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'company_pinned_message';
	}

	public function rules(): array
	{
		return [
			[['chat_member_message_id', 'company_id', 'created_by_id'], 'required'],
			[['chat_member_message_id', 'company_id', 'created_by_id'], 'integer'],
			[['chat_member_message_id'], 'exist', 'targetClass' => ChatMemberMessage::class, 'targetAttribute' => ['chat_member_message_id' => 'id']],
			[['company_id'], 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
			[['created_by_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['created_by_id' => 'id']],
		];
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	public function getChatMemberMessage(): ChatMemberMessageQuery
	{
		/** @var ChatMemberMessageQuery */
		return $this->hasOne(ChatMemberMessage::class, ['id' => 'chat_member_message_id']);
	}

	public function getCreatedBy(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'created_by_id']);
	}

	public static function find(): CompanyPinnedMessageQuery
	{
		return (new CompanyPinnedMessageQuery(self::class))->notDeleted();
	}
}
