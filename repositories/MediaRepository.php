<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ChatMemberMessage;
use app\models\Media;
use app\models\Relation;
use yii\data\ActiveDataProvider;

class MediaRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndModel(int $id, int $modelId, string $modelType): Media
	{
		return Media::find()
		               ->byId($id)
		               ->notDeleted()
		               ->byMorph($modelId, $modelType)
		               ->oneOrThrow();
	}

	public function findModelsByMemberChatId(int $toMemberChatId, int $fromMemberChatId, ?string $extension = null): ActiveDataProvider
	{
		$query = Media::find()
		     ->where(['in', 'id', Relation::find()
			     ->select('second_id')
			     ->from('relation')
			     ->where(['second_type' => 'media', 'first_type' => 'chat_member_message'])
			     ->andWhere(['in', 'first_id', ChatMemberMessage::find()
					 ->select('id')
					 ->where(['to_chat_member_id' => $toMemberChatId, 'from_chat_member_id' => $fromMemberChatId])
					 ->notDeleted()
			     ])
		     ])
			->andFilterWhere(['like', 'extension', $extension])
			->notDeleted();

		return new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 25
			],
			'sort' => [
				'enableMultiSort' => true,
				'defaultOrder' => [
					'default' => SORT_DESC,
				],

				'attributes' => [
					'created_at',
					'default' => [
						'asc' => [
							'created_at' => SORT_ASC
						],
						'desc' => [
							'created_at' => SORT_DESC
						],
					]
				]
			]
		]);
	}
}