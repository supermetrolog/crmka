<?php

declare(strict_types=1);

namespace app\models\forms\Request;

use app\dto\Request\CreateRequestDto;
use app\dto\Request\UpdateRequestDto;
use app\enum\Request\RequestDealTypeEnum;
use app\enum\Request\RequestPassiveWhyEnum;
use app\enum\Request\RequestStatusEnum;
use app\helpers\ArrayHelper;
use app\helpers\DateTimeHelper;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;
use app\models\Company\Company;
use app\models\Contact;
use app\models\Request;
use app\models\User\User;
use Exception;

class RequestForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $company_id;
	public $contact_id;
	public $consultant_id;
	public $dealType;
	public $minArea;
	public $maxArea;
	public $minCeilingHeight;
	public $maxCeilingHeight;
	public $distanceFromMKAD;
	public $pricePerFloor;
	public $trainLineLength;
	public $electricity;
	public $unknownMovingDate;
	public $outside_mkad;
	public $region_neardy;
	public $passive_why;
	public $distanceFromMKADnotApplicable;
	public $firstFloorOnly;
	public $expressRequest;
	public $heated;
	public $antiDustOnly;
	public $trainLine;
	public $haveCranes;
	public $water;
	public $sewerage;
	public $gaz;
	public $steam;
	public $shelving;

	public $name;
	public $description;
	public $passive_why_comment;
	public $status;
	public $movingDate;

	public $direction_ids           = [];
	public $district_ids            = [];
	public $gate_types              = [];
	public $object_classes          = [];
	public $region_ids              = [];
	public $object_type_ids         = [];
	public $object_type_general_ids = [];

	public function rules(): array
	{
		return [
			[['name', 'passive_why_comment'], 'string', 'max' => 255],
			[['description'], 'string'],
			[['company_id'], 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
			[['contact_id'], 'exist', 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
			[['consultant_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			[['company_id', 'consultant_id', 'dealType', 'minCeilingHeight', 'minArea', 'maxArea'], 'required'],
			[
				[
					'outside_mkad', 'region_neardy', 'distanceFromMKADnotApplicable',
					'firstFloorOnly', 'antiDustOnly', 'expressRequest',
					'heated', 'water', 'sewerage', 'gaz', 'steam', 'shelving', 'trainLine', 'haveCranes',
				],
				'boolean'],
			[
				[
					'contact_id', 'company_id', 'consultant_id',
					'dealType', 'status', 'pricePerFloor', 'passive_why', 'unknownMovingDate',
					'distanceFromMKAD',
					'maxArea', 'maxCeilingHeight'
				], 'integer'
			],
			['dealType', EnumValidator::class, 'enumClass' => RequestDealTypeEnum::class],
			['status', EnumValidator::class, 'enumClass' => RequestStatusEnum::class],
			['passive_why', EnumValidator::class, 'enumClass' => RequestPassiveWhyEnum::class],
			['unknownMovingDate', 'in', 'range' => Request::getUnknownMovingDateReasons()],
			[['movingDate'], 'safe'],
			[['direction_ids', 'district_ids', 'gate_types', 'object_classes', 'object_type_ids', 'object_type_general_ids', 'region_ids'], 'each', 'rule' => ['integer']],
			['maxArea', 'compare', 'compareAttribute' => 'minArea', 'operator' => '>', 'message' => 'Максимальная площадь не может быть меньше минимальной'],
			['maxCeilingHeight', 'compare', 'compareAttribute' => 'minCeilingHeight', 'operator' => '>', 'message' => 'Максимальная высота потолка не может быть меньше минимальной'],
			['minArea', 'integer', 'min' => 1, 'message' => 'Минимальная площадь не может быть меньше 1'],
			['minCeilingHeight', 'integer', 'min' => 1, 'message' => 'Минимальная высота потолка не может быть меньше 1'],
			['electricity', 'integer', 'min' => 1, 'message' => 'Мощность электроэнергии не может быть меньше 1'],
			['trainLineLength', 'integer', 'min' => 1, 'message' => 'Длина линии не может быть меньше 1'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'name'                          => 'Название',
			'description'                   => 'Описание',
			'company_id'                    => 'ID компании',
			'contact_id'                    => 'ID контакта',
			'consultant_id'                 => 'ID консультанта',
			'dealType'                      => 'Тип сделки',
			'minArea'                       => 'Минимальная площадь',
			'maxArea'                       => 'Максимальная площадь',
			'minCeilingHeight'              => 'Минимальная высота потолка',
			'maxCeilingHeight'              => 'Максимальная высота потолка',
			'distanceFromMKAD'              => 'Удаленность от МКАД',
			'distanceFromMKADnotApplicable' => 'Расстояние от МКАД не применимо',
			'firstFloorOnly'                => 'Только первый этаж',
			'expressRequest'                => 'Срочный запрос',
			'heated'                        => 'Отопление',
			'water'                         => 'Водоснабжение',
			'sewerage'                      => 'Канализация',
			'gaz'                           => 'Газ',
			'steam'                         => 'Пар',
			'shelving'                      => 'Стеллажи',
			'haveCranes'                    => 'Краны',
			'pricePerFloor'                 => 'Цена за пол',
			'antiDustOnly'                  => 'Только анти-пыль',
			'trainLine'                     => 'Ж/Д линия',
			'trainLineLength'               => 'Длина Ж/Д линии',
			'electricity'                   => 'Электричество',
			'unknownMovingDate'             => 'Причина отсутствия даты переезда',
			'outside_mkad'                  => 'За МКАДом',
			'region_neardy'                 => 'Учитывать регионы рядом с МКАД',
			'passive_why'                   => 'Причина пассива',
			'passive_why_comment'           => 'Комментарий к пассиву',
			'status'                        => 'Статус',
			'movingDate'                    => 'Дата переезда',

			'direction_ids'           => 'Направления',
			'district_ids'            => 'Округа',
			'gate_types'              => 'Типы ворот',
			'object_classes'          => 'Классы объектов',
			'object_type_ids'         => 'Назначения объектов',
			'object_type_general_ids' => 'Типы объектов',
			'region_ids'              => 'Регионы',
		];
	}

	public function scenarios(): array
	{
		$common = [
			'name',
			'description',
			'contact_id',
			'consultant_id',
			'dealType',
			'minArea',
			'maxArea',
			'minCeilingHeight',
			'maxCeilingHeight',
			'distanceFromMKAD',
			'distanceFromMKADnotApplicable',
			'firstFloorOnly',
			'expressRequest',
			'heated',
			'water',
			'sewerage',
			'gaz',
			'steam',
			'shelving',
			'haveCranes',
			'pricePerFloor',
			'antiDustOnly',
			'trainLine',
			'trainLineLength',
			'electricity',
			'unknownMovingDate',
			'outside_mkad',
			'region_neardy',
			'movingDate',
			'direction_ids',
			'district_ids',
			'gate_types',
			'object_classes',
			'object_type_ids',
			'object_type_general_ids',
			'region_ids',
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'company_id'],
			self::SCENARIO_UPDATE => [...$common, 'passive_why', 'passive_why_comment', 'status'],
		];
	}

	/**
	 * @return CreateRequestDto|UpdateRequestDto
	 * @throws Exception
	 */
	public function getDto()
	{
		$commonAttrs = [
			'name'                          => $this->name,
			'description'                   => $this->description,
			'contact_id'                    => $this->contact_id,
			'consultant_id'                 => $this->consultant_id,
			'dealType'                      => $this->dealType,
			'minArea'                       => $this->minArea,
			'maxArea'                       => $this->maxArea,
			'minCeilingHeight'              => $this->minCeilingHeight,
			'maxCeilingHeight'              => $this->maxCeilingHeight,
			'distanceFromMKAD'              => $this->distanceFromMKAD,
			'distanceFromMKADnotApplicable' => $this->distanceFromMKADnotApplicable,
			'firstFloorOnly'                => $this->firstFloorOnly,
			'expressRequest'                => $this->expressRequest,
			'heated'                        => $this->heated,
			'water'                         => $this->water,
			'sewerage'                      => $this->sewerage,
			'gaz'                           => $this->gaz,
			'steam'                         => $this->steam,
			'shelving'                      => $this->shelving,
			'haveCranes'                    => $this->haveCranes,
			'pricePerFloor'                 => $this->pricePerFloor,
			'antiDustOnly'                  => $this->antiDustOnly,
			'trainLine'                     => $this->trainLine,
			'trainLineLength'               => $this->trainLineLength,
			'electricity'                   => $this->electricity,
			'unknownMovingDate'             => $this->unknownMovingDate,
			'outside_mkad'                  => $this->outside_mkad,
			'region_neardy'                 => $this->region_neardy,
			'movingDate'                    => DateTimeHelper::tryMake($this->movingDate),

			'direction_ids'           => $this->direction_ids,
			'district_ids'            => $this->district_ids,
			'gate_types'              => $this->gate_types,
			'object_classes'          => $this->object_classes,
			'object_type_ids'         => $this->object_type_ids,
			'object_type_general_ids' => $this->object_type_general_ids,
			'region_ids'              => $this->region_ids
		];

		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateRequestDto(
				ArrayHelper::merge(
					$commonAttrs,
					[
						'company_id' => $this->company_id
					]
				)
			);
		}

		return new UpdateRequestDto(
			ArrayHelper::merge(
				$commonAttrs,
				[
					'status'              => $this->status,
					'passive_why'         => $this->passive_why,
					'passive_why_comment' => $this->passive_why_comment,
				]
			)
		);
	}
}