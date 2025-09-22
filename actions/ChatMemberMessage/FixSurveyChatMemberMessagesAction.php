<?php

declare(strict_types=1);

namespace app\actions\ChatMemberMessage;

use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\kernel\common\actions\Action;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ChatMemberMessage;
use app\models\EntityMessageLink;
use app\models\User\User;

class FixSurveyChatMemberMessagesAction extends Action
{

	public function run(): void
	{
		$this->info('Start transfer survey comment messages from SystemChatMember to survey author');

		$systemChatMemberId = User::find()->system()->one()->chatMember->id;

		$this->info('Current SystemChatMember ID: #' . $systemChatMemberId);

		$query = ChatMemberMessage::find()
		                          ->alias('cmm')
		                          ->innerJoin(['cpm' => EntityMessageLink::find()->byKind(EntityMessageLinkKindEnum::COMMENT)], 'cpm.chat_member_message_id = cmm.id')
		                          ->innerJoinWith(['surveys' => function (SurveyQuery $query) {
			                          $query->with(['user.chatMember']);
		                          }])
		                          ->with(['toChatMember', 'fromChatMember.user'])
		                          ->andWhere([
			                          'cmm.from_chat_member_id' => $systemChatMemberId,
			                          'cmm.template'            => ChatMemberMessage::SURVEY_TEMPLATE
		                          ])
		                          ->andWhere('cmm.deleted_at is null');

		$count = (int)$query->count();

		if ($count === 0) {
			$this->info('No messages found, skipping...');

			return;
		}

		$this->infof('Found %d messages', $count);

		$transferred = 0;

		/** @var ChatMemberMessage $message */
		foreach ($query->each() as $message) {
			$survey = $message->surveys[0] ?? null;

			if ($survey) {
				$message->updateAttributes(['from_chat_member_id' => $survey->user->chatMember->id]);

				$transferred++;

				$this->commentf('Transferring message #%d from SYSTEM to %s', $message->id, $survey->user->userProfile->getMediumName());
			}

		}

		$this->infof('Complete. Transferred %d messages', $transferred);
	}
}