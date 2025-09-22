<?php

namespace app\models\views;

use app\models\ActiveQuery\RelationQuery;
use app\models\Call;
use app\models\ChatMemberMessage;
use app\models\CommercialOffer;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\OfferMix;
use app\models\Relation;
use app\models\Request;
use app\models\User\User;

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
class OfferMixSearchView extends OfferMix
{
	public int $unread_message_count  = 0;
	public int $complex_objects_count = 0;


	public function fields(): array
	{
		$fields = parent::fields();

		$fields['unread_message_count']  = fn() => $this->unread_message_count;
		$fields['complex_objects_count'] = fn() => $this->complex_objects_count;

		return $fields;
	}
}
