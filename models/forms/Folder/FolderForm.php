<?php

declare(strict_types=1);

namespace app\models\forms\Folder;

use app\dto\Folder\CreateFolderDto;
use app\dto\Folder\UpdateFolderDto;
use app\kernel\common\models\Form\Form;
use app\models\User;

class FolderForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $user_id;
	public $name;
	public $color;
	public $icon;
	public $category;

	public function rules(): array
	{
		return [
			[['user_id', 'name', 'category'], 'required'],
			[['user_id'], 'integer'],
			[['name', 'icon'], 'string', 'max' => 64],
			[['color'], 'string', 'max' => 6, 'min' => 6],
			[['category'], 'string', 'max' => 255],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'user_id'  => 'ID пользователя',
			'name'     => 'Название',
			'color'    => 'Цвет',
			'icon'     => 'Иконка',
			'category' => 'Категория отображения'
		];
	}

	public function scenarios(): array
	{
		$common = [
			'name',
			'color',
			'icon',
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'user_id', 'category'],
			self::SCENARIO_UPDATE => $common
		];
	}

	/**
	 * @return CreateFolderDto|UpdateFolderDto
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateFolderDto([
				'user'     => User::find()->byId((int)$this->user_id)->one(),
				'name'     => $this->name,
				'color'    => $this->color,
				'icon'     => $this->icon,
				'category' => $this->category,
			]);
		}

		return new UpdateFolderDto([
			'name'  => $this->name,
			'color' => $this->color,
			'icon'  => $this->icon
		]);
	}
}