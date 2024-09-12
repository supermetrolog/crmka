<?php

namespace app\models\views;

use app\models\ActiveQuery\CallQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\Call;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\CommercialOffer;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\OfferMix;
use app\models\Relation;
use app\models\Request;
use app\models\User;
use yii\base\ErrorException;

/**
 * This is the model class for table "chat_member".
 *
 * @property int                   $id
 * @property string                $model_type
 * @property int                   $model_id
 * @property string                $created_at
 * @property string                $updated_at
 * @property int|null              $pinned_chat_member_message_id
 *
 * @property ChatMemberMessage[]   $fromChatMemberMessages
 * @property ChatMemberMessage[]   $toChatMemberMessages
 * @property ChatMemberMessage[]   $messages
 * @property User|OfferMix|Request $model
 * @property OfferMix              $offerMix
 * @property User                  $user
 * @property Request               $request
 * @property CommercialOffer       $commercialOffer
 * @property ObjectChatMember      $objectChatMember
 * @property Objects               $object
 * @property ChatMemberMessage     $pinnedChatMemberMessage
 * @property Relation[]            $relationFirst
 * @property Call[]                $calls
 * @property-read RelationQuery    $lastCallRelationFirst
 * @property Call                  $lastCall
 */
class ChatMemberSearchView extends ChatMember
{
	public ?int  $last_call_rel_id          = null;
	public ?bool $is_linked                 = null;
	public ?int  $unread_task_count         = null;
	public ?int  $unread_notification_count = null;
	public ?int  $unread_message_count      = null;

	/**
	 * @return RelationQuery
	 * @throws ErrorException
	 */
	public function getLastCallRelationFirst(): RelationQuery
	{
		/** @var RelationQuery $query */
		$query = $this->hasOne(Relation::class, [
			'first_id'   => 'id',
			'first_type' => 'morph',
			'id'         => 'last_call_rel_id'
		])->from([Relation::tableName() => Relation::getTable()]);

		return $query;
	}

	/**
	 * @return CallQuery
	 * @throws ErrorException
	 */
	public function getLastCall(): CallQuery
	{
		/** @var CallQuery $query */
		$query = $this->morphHasOneVia(Call::class, 'id', 'second')
		              ->via('lastCallRelationFirst');

		return $query;
	}
}
