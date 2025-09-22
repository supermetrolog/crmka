<?php

namespace app\models;

use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\EntityMessageLinkQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\Company\Company;
use app\models\User\User;
use InvalidArgumentException;
use yii\db\ActiveQuery;

/**
 * @property int                    $id
 * @property int                    $entity_id
 * @property string                 $entity_type
 * @property int                    $created_by_id
 * @property int                    $chat_member_message_id
 * @property string                 $kind
 * @property int                    $created_at
 * @property int                    $updated_at
 * @property int                    $deleted_at
 *
 * @property-read ChatMemberMessage $chatMemberMessage
 * @property-read Company           $company
 * @property-read Request           $request
 * @property-read Objects           $object
 * @property-read User              $createdBy
 */
class EntityMessageLink extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'entity_message_link';
	}

	public function rules(): array
	{
		return [
			[['chat_member_message_id', 'entity_id', 'entity_type', 'created_by_id', 'kind'], 'required'],
			[['chat_member_message_id', 'entity_id', 'created_by_id'], 'integer'],
			[['entity_type', 'kind'], 'string'],
			['kind', EnumValidator::class, 'enumClass' => EntityMessageLinkKindEnum::class],
			[['chat_member_message_id'], 'exist', 'targetClass' => ChatMemberMessage::class, 'targetAttribute' => ['chat_member_message_id' => 'id']],
			[['created_by_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['created_by_id' => 'id']],
		];
	}

	public function morphBelongTo($class, string $column = 'id', string $morphColumn = 'entity', string $ownerColumn = 'morph'): ActiveQuery
	{
		return parent::morphBelongTo($class, $column, $morphColumn);
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->morphBelongTo(Company::class);
	}

	public function getRequest(): RequestQuery
	{
		/** @var RequestQuery */
		return $this->morphBelongTo(Request::class);
	}

	public function getObject(): ActiveQuery
	{
		/** @var ActiveQuery */
		return $this->morphBelongTo(Objects::class);
	}

	/** @return Company|Request|Objects */
	public function getEntity()
	{
		switch ($this->entity_type) {
			case Company::getMorphClass():
				return $this->company;
			case Request::getMorphClass():
				return $this->request;
			case Objects::getMorphClass():
				return $this->object;
			default:
				throw new InvalidArgumentException("Unexpected EntityMessageLink entity_type: " . $this->entity_type);
		}
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

	public static function find(): EntityMessageLinkQuery
	{
		return (new EntityMessageLinkQuery(self::class))->notDeleted();
	}
}
