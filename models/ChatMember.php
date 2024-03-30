<?php

namespace app\models;

use app\helpers\MatchHelper;
use app\kernel\common\models\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\OfferMixQuery;
use Exception;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "chat_member".
 *
 * @property int                 $id
 * @property string              $model_type
 * @property int                 $model_id
 * @property string              $created_at
 * @property string              $updated_at
 *
 * @property ChatMemberMessage[] $chatFromMemberMessages
 * @property ChatMemberMessage[] $chatToMemberMessages0
 * @property User|OfferMix       $model
 * @property OfferMix          $offerMix
 * @property User                $user
 *
 */
class ChatMember extends AR
{

	public static function tableName(): string
	{
		return 'chat_member';
	}

	public function rules(): array
	{
		return [
			[['model_type', 'model_id'], 'required'],
			[['model_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['model_type'], 'string', 'max' => 255],
			[['model_type', 'model_id'], 'unique', 'targetAttribute' => ['model_type', 'model_id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'model_type' => 'Model Type',
			'model_id'   => 'Model ID',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery|ChatMemberMessageQuery
	 */
	public function getFromChatMemberMessages(): ChatMemberMessageQuery
	{
		return $this->hasMany(ChatMemberMessage::className(), ['from_chat_member_id' => 'id']);
	}

	/**
	 * @return ActiveQuery|ChatMemberMessageQuery
	 */
	public function getToChatMemberMessages0(): ChatMemberMessageQuery
	{
		return $this->hasMany(ChatMemberMessage::className(), ['to_chat_member_id' => 'id']);
	}

	/**
	 * @throws Exception
	 */
	public function getModel(): ActiveQuery
	{
		return $this->hasOne($this->getMorphClass(), ['id' => 'model_id']);
	}

	/**
	 * @throws Exception
	 */
	public function getMorphClass(): string
	{
		return MatchHelper::match([
			User::tableName()     => User::class,
			OfferMix::tableName() => OfferMix::class,
		], $this->model_type);
	}

	/**
	 * @return OfferMixQuery|ActiveQuery
	 */
	public function getOfferMix(): OfferMixQuery
	{
		return $this->hasOne(OfferMix::class, ['id' => 'model_id'])->innerJoinWith(['chatMember' => function (ChatMemberQuery $query) {
			$query->from('crmka.chat_member');
		}]);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'model_id'])->innerJoinWith(['chatMember' => function (ChatMemberQuery $query) {
			$query->from('crmka.chat_member');
		}]);
	}

	public static function find(): ChatMemberQuery
	{
		return new ChatMemberQuery(get_called_class());
	}
}
