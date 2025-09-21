<?php

declare(strict_types=1);

namespace app\models\forms\Contact;

use app\dto\ContactPosition\CreateContactPositionDto;
use app\dto\ContactPosition\UpdateContactPositionDto;
use app\kernel\common\models\Form\Form;
use app\models\ContactPosition;
use app\models\User\User;

class ContactPositionForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $created_by_id;
	public $slug;
	public $name;
	public $short_name;
	public $description;
	public $color;
	public $icon;
	public $is_active;
	public $sort_order;

	public function rules(): array
	{
		return [
			[['name', 'sort_order', 'is_active'], 'required'],
			[['created_by_id', 'sort_order'], 'integer'],
			[['name', 'slug', 'icon'], 'string', 'max' => 64],
			[['short_name'], 'string', 'max' => 32],
			[['description'], 'string', 'max' => 128],
			[['color'], 'string', 'max' => 6],
			[['is_active'], 'boolean'],
			[['created_by_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'slug',
			'name',
			'short_name',
			'description',
			'icon',
			'color',
			'sort_order',
			'is_active'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'created_by_id'],
			self::SCENARIO_UPDATE => $common
		];
	}

	protected function getCreatedBy(): ?User
	{
		return $this->created_by_id ? User::findOne($this->created_by_id) : null;
	}


	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateContactPositionDto([
				'createdBy'   => $this->getCreatedBy(),
				'name'        => $this->name,
				'slug'        => $this->slug,
				'short_name'  => $this->short_name,
				'description' => $this->description,
				'icon'        => $this->icon,
				'color'       => $this->color,
				'sort_order'  => $this->sort_order ?? ContactPosition::DEFAULT_SORT_ORDER,
				'is_active'   => $this->is_active
			]);
		}

		return new UpdateContactPositionDto([
			'name'        => $this->name,
			'slug'        => $this->slug,
			'short_name'  => $this->short_name,
			'description' => $this->description,
			'icon'        => $this->icon,
			'color'       => $this->color,
			'sort_order'  => $this->sort_order ?? ContactPosition::DEFAULT_SORT_ORDER,
			'is_active'   => $this->is_active
		]);
	}
}