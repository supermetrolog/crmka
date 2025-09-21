<?php

declare(strict_types=1);

namespace app\models\forms\Equipment;

use app\dto\Equipment\CreateEquipmentDto;
use app\dto\Equipment\UpdateEquipmentDto;
use app\kernel\common\models\Form\Form;
use app\models\Company\Company;
use app\models\Contact;
use app\models\Equipment;
use app\models\User\User;
use Exception;

class EquipmentForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $name;
	public $address;
	public $description;
	public $company_id;
	public $contact_id;
	public $consultant_id;
	public $preview;
	public $files;
	public $photos;
	public $category;
	public $availability;
	public $delivery;
	public $deliveryPrice;
	public $price;
	public $benefit;
	public $tax;
	public $count;
	public $state;
	public $status;
	public $passive_type;
	public $passive_comment;
	public $created_by_type;
	public $created_by_id;

	public function rules(): array
	{
		return [
			[['description', 'passive_comment'], 'string'],
			[['company_id', 'contact_id', 'consultant_id', 'created_by_type', 'created_by_id'], 'required'],
			[['company_id', 'contact_id', 'consultant_id', 'category', 'availability', 'delivery', 'deliveryPrice', 'price', 'benefit', 'tax', 'count', 'state', 'status', 'passive_type', 'created_by_id'], 'integer'],
			[['name'], 'string', 'max' => 60],
			[['address', 'created_by_type'], 'string', 'max' => 255],
			['category', 'in', 'range' => Equipment::getCategories()],
			['availability', 'in', 'range' => Equipment::getAvailabilities()],
			['delivery', 'in', 'range' => Equipment::getDeliveries()],
			['benefit', 'in', 'range' => Equipment::getBenefits()],
			['tax', 'in', 'range' => Equipment::getTaxes()],
			['state', 'in', 'range' => Equipment::getStates()],
			['status', 'in', 'range' => Equipment::getStatuses()],
			['passive_type', 'in', 'range' => Equipment::getPassiveTypes()],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'name',
			'address',
			'description',
			'company_id',
			'contact_id',
			'consultant_id',
			'category',
			'availability',
			'delivery',
			'deliveryPrice',
			'price',
			'benefit',
			'tax',
			'count',
			'state',
			'status',
			'passive_type',
			'passive_comment',
		];

		return [
			self::SCENARIO_CREATE => $common,
			self::SCENARIO_UPDATE => $common,
		];
	}

	/**
	 * @return CreateEquipmentDto|UpdateEquipmentDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateEquipmentDto([
					'name'            => $this->name,
					'address'         => $this->address,
					'description'     => $this->description,
					'company_id'      => $this->company_id,
					'contact_id'      => $this->contact_id,
					'consultant_id'   => $this->consultant_id,
					'category'        => $this->category,
					'availability'    => $this->availability,
					'delivery'        => $this->delivery,
					'deliveryPrice'   => $this->deliveryPrice,
					'price'           => $this->price,
					'benefit'         => $this->benefit,
					'tax'             => $this->tax,
					'count'           => $this->count,
					'state'           => $this->state,
					'status'          => $this->status,
					'passive_type'    => $this->passive_type,
					'passive_comment' => $this->passive_comment,
					'created_by_type' => $this->created_by_type,
					'created_by_id'   => $this->created_by_id,
				]);

			default:
				return new UpdateEquipmentDto([
					'name'            => $this->name,
					'address'         => $this->address,
					'description'     => $this->description,
					'company_id'      => $this->company_id,
					'contact_id'      => $this->contact_id,
					'consultant_id'   => $this->consultant_id,
					'category'        => $this->category,
					'availability'    => $this->availability,
					'delivery'        => $this->delivery,
					'deliveryPrice'   => $this->deliveryPrice,
					'price'           => $this->price,
					'benefit'         => $this->benefit,
					'tax'             => $this->tax,
					'count'           => $this->count,
					'state'           => $this->state,
					'status'          => $this->status,
					'passive_type'    => $this->passive_type,
					'passive_comment' => $this->passive_comment,
				]);
		}
	}
}