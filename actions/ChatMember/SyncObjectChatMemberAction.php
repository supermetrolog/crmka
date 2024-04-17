<?php

declare(strict_types=1);

namespace app\actions\ChatMember;

use app\dto\ChatMember\CreateChatMemberDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\actions\Action;
use app\models\ChatMember;
use app\models\CommercialOffer;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\Request;
use app\usecases\ChatMember\ChatMemberService;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\db\Exception;

class  SyncObjectChatMemberAction extends Action
{
	private ChatMemberService $service;

	public function __construct($id, $controller, ChatMemberService $service, array $config = [])
	{
		$this->service = $service;
		parent::__construct($id, $controller, $config);
	}

	/**
	 * @throws ErrorException
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function run(): void
	{
		$this->syncObjectChatMember();
		$this->syncChatMember();
	}

	/**
	 * @throws Exception
	 * @throws ErrorException
	 */
	private function syncChatMember(): void
	{
		$query = ObjectChatMember::find()
		                         ->joinWith(['chatMember'])
		                         ->andWhereNull(ChatMember::field('id'));

		/** @var ObjectChatMember $model */
		foreach ($query->each(1000) as $model) {
			$this->service->upsert(new CreateChatMemberDto([
				'model_id'   => $model->id,
				'model_type' => ObjectChatMember::getMorphClass()
			]));

			$this->infof('Created object chat member with ID: %d', $model->id);
		}
	}

	/**
	 * @return void
	 * @throws ErrorException
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	private function syncObjectChatMember(): void
	{
		$dealTypeSubQuery = CommercialOffer::find()
		                                   ->andWhereColumn(CommercialOffer::field('object_id'), Objects::field('id'))
		                                   ->notDeleted();

		$rentOrSaleSubQuery      = (clone $dealTypeSubQuery)->rentOrSale();
		$subleaseSubQuery        = (clone $dealTypeSubQuery)->sublease();
		$responseStorageSubQuery = (clone $dealTypeSubQuery)->responseStorage();


		$params                        = [];
		$rentOrSaleExistsSubQuery      = CommercialOffer::getDb()->getQueryBuilder()->buildExistsCondition('EXISTS', [$rentOrSaleSubQuery], $params);
		$subleaseExistsSubQuery        = CommercialOffer::getDb()->getQueryBuilder()->buildExistsCondition('EXISTS', [$subleaseSubQuery], $params);
		$responseStorageExistsSubQuery = CommercialOffer::getDb()->getQueryBuilder()->buildExistsCondition('EXISTS', [$responseStorageSubQuery], $params);

		$query = Objects::find()
		                ->select([
			                Objects::field('*'),
			                'rentOrSale'      => $rentOrSaleExistsSubQuery,
			                'sublease'        => $subleaseExistsSubQuery,
			                'responseStorage' => $responseStorageExistsSubQuery,
		                ])
		                ->addParams($params);

		/** @var Objects $object */
		foreach ($query->each(1000) as $object) {
			if ($object->rentOrSale) {
				$this->storeObjectChatMember($object, 'rent_or_sale');
			}
			if ($object->sublease) {
				$this->storeObjectChatMember($object, 'sublease');
			}
			if ($object->responseStorage) {
				$this->storeObjectChatMember($object, 'response_storage');
			}
		}
	}

	/**
	 * @throws Exception
	 */
	private function storeObjectChatMember(Objects $object, string $type): void
	{
		ObjectChatMember::upsert(
			ObjectChatMember::tableName(),
			[
				'object_id'  => $object->id,
				'type'       => $type,
				'created_at' => DateTimeHelper::nowf(),
				'updated_at'  => DateTimeHelper::nowf(),
			],
			['updated_at' => DateTimeHelper::nowf()]
		);

		$this->infof('Created object chat member. Object ID: %d; Type: %s', $object->id, $type);
	}
}