<?php

namespace app\models;

use app\helpers\MatchHelper;
use app\kernel\common\models\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\OfferMixQuery;
use Exception;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "chat_member".
 *
 * @property int                   $id
 * @property string                $model_type
 * @property int                   $model_id
 * @property string                $created_at
 * @property string                $updated_at
 *
 * @property ChatMemberMessage[]   $chatFromMemberMessages
 * @property ChatMemberMessage[]   $chatToMemberMessages0
 * @property User|OfferMix|Request $model
 * @property OfferMix              $offerMix
 * @property User                  $user
 * @property Request               $request
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
	 * //     * @throws Exception
	 * //     */
//	public function getModel(): ActiveQuery
//	{
//		return $this->hasOne($this->getMorphClass(), ['id' => 'model_id']);
//	}

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
	 * @throws ErrorException
	 */
	public function getOfferMix(): OfferMixQuery
	{
		return $this->morphBelongTo(OfferMix::class, 'original_id');
	}

	/**
	 * @throws ErrorException
	 */
	public function getRequest(): ActiveQuery
	{
		return $this->morphBelongTo(Request::class);
	}

	/**
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function getUser(): ActiveQuery
	{
		return $this->morphBelongTo(User::class);
	}

	public function getModel(): ActiveRecord
	{
		return $this->request ?? $this->user ?? $this->offerMix;
	}

	public static function find(): ChatMemberQuery
	{
		return new ChatMemberQuery(get_called_class());
	}
}
